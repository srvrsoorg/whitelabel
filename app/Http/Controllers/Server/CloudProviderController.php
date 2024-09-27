<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Configuration\CloudProvider;
use App\Enums\CloudProvider as CloudProviderEnums;
use App\Http\Utilities\Client;
use Aws\Exception\AwsException;
use App\Http\Helper;

class CloudProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cloudProviders = CloudProvider::select('id', 'provider', 'visible')->whereHas('plans', function($query){
            $query->where('visible', true);
        })->whereVisible(true)->get();

        // Success response
        return response()->json([
            "cloud_providers" => $cloudProviders,
        ],200);
    }

    // Helper function to handle error responses
    function errorResponse($message, $statusCode = 500) {
        return response()->json(['message' => $message], $statusCode);
    }

    /**
     * Retrieve the list of distinct regions available for a specified cloud provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    public function regions(Request $request, CloudProvider $cloudProvider)
    {
        try {
            // Fetch regions available for the cloud provider
            $regions = $cloudProvider->plans()->where('visible', true)->distinct('region')->pluck('region')->toArray();

            switch ($cloudProvider->provider) {
                case CloudProviderEnums::LIGHTSAIL():
                    // Handle Lightsail regions
                    return $this->handleLightsailRegions($request, $cloudProvider, $regions);
                case CloudProviderEnums::DIGITALOCEAN():
                    // Handle DigitalOcean regions
                    return $this->handleDigitalOceanRegions($request, $cloudProvider, $regions);
                case CloudProviderEnums::LINODE():
                    // Handle Linode regions
                    return $this->handleLinodeRegions($request, $cloudProvider, $regions);
                case CloudProviderEnums::HETZNER():
                    // Handle Hetzner regions
                    return $this->handleHetznerRegions($request, $cloudProvider, $regions);
                case CloudProviderEnums::VULTR():
                    // Handle Vultr regions
                    return $this->handleVultrRegions($request, $cloudProvider, $regions);
                default:
                    return $this->errorResponse('Invalid cloud platform.', 400);
            }

        } catch (AwsException $aws) {
            report($aws);
            return $this->errorResponse($aws->getAwsErrorMessage(), 500);
        } catch (Exception $e) {
            report($e);
            return $this->errorResponse('An unexpected error occurred!', 500);
        }
    }

    /**
     * Handle the processing of Lightsail regions based on the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  array  $regions
     * @return \Illuminate\Http\Response
     */
    protected function handleLightsailRegions(Request $request, CloudProvider $cloudProvider, $regions)
    {
        if ($request->has('region')) {
            $region = $request->input('region');

            $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, $region);
            $data = $client->getRegions([
                'includeAvailabilityZones' => true
            ]);

            $data = collect($data['regions'])->first(function ($row) use ($region) {
                return $row['name'] == $region;
            });

            // Success response
            return response()->json([
                'region_zones' => $data['availabilityZones'],
                'region' => $region,
                'sizes' => null
            ], 200);
        } else {
            $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, 'ap-south-1');
            $data = $client->getRegions([
                'includeAvailabilityZones' => true
            ]);

            $filteredRegions = collect($data['regions'])->map(function ($row) use ($regions) {
                if (in_array($row['name'], $regions)) {
                    return [
                        "name" => $row['displayName'],
                        "value" => $row['name'],
                        "available" => true
                    ];
                }
            })->filter();

            return response()->json($filteredRegions);
        }
    }

    /**
     * Handle the processing of Digitalocean regions based on the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  array  $regions
     * @return \Illuminate\Http\Response
     */
    protected function handleDigitalOceanRegions(Request $request, CloudProvider $cloudProvider, $regions)
    {
        $data = Client::digitalOcean("regions", "GET", $cloudProvider->access_key);
        if (isset($data['error'])) {
            return $this->errorResponse($data['message'], 500);
        }

        $data = json_decode($data);

        $filteredRegions = collect($data->regions)->map(function ($row) use ($regions) {
            if (in_array($row->slug, $regions)) {
                return [
                    "name" => $row->name,
                    "value" => $row->slug,
                    "available" => count($row->sizes) > 0,
                    "sizes" => $row->sizes
                ];
            }
        })->filter();

        return response()->json($filteredRegions);
    }

    /**
     * Handle the processing of Linode regions based on the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  array  $regions
     * @return \Illuminate\Http\Response
     */
    protected function handleLinodeRegions(Request $request, CloudProvider $cloudProvider, $regions)
    {
        $data = Client::linode("regions", "GET", $cloudProvider->access_key);
        if (isset($data['error'])) {
            return $this->errorResponse($data['message'], 500);
        }

        $data = json_decode($data);

        $filteredRegions = collect($data->data)->map(function ($row) use ($regions) {
            if (in_array($row->id, $regions)) {
                return [
                    "name" => $row->label,
                    "value" => $row->id,
                    "available" => $row->status == "ok"?1:0,
                    "sizes" => null
                ];
            }
        })->filter();

        return response()->json($filteredRegions);
    }

    /**
     * Handle the processing of Hetzner regions based on the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  array  $regions
     * @return \Illuminate\Http\Response
     */
    protected function handleHetznerRegions(Request $request, CloudProvider $cloudProvider, $regions)
    {
        $data = Client::hetzner("locations", "GET", $cloudProvider->access_key);
        if (isset($data['error'])) {
            return $this->errorResponse($data['message'], 500);
        }

        $data = json_decode($data);

        $filteredRegions = collect($data->locations)->map(function ($row) use ($regions) {
            if (in_array($row->name, $regions)) {
                return [
                    "name" => $row->city,
                    "value" => $row->name,
                    "zone" => $row->network_zone,
                    "available" => true
                ];
            }
        })->filter();

        return response()->json($filteredRegions);
    }

    /**
     * Handle the processing of Vultr regions based on the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  array  $regions
     * @return \Illuminate\Http\Response
     */
    protected function handleVultrRegions(Request $request, CloudProvider $cloudProvider, $regions)
    {
        $data = Client::vultr("regions", "GET", $cloudProvider->access_key);
        if (isset($data['error'])) {
            return $this->errorResponse($data['message'], 500);
        }

        $data = json_decode($data);

        $filteredRegions = collect($data->regions)->map(function ($row) use ($regions) {
            if (in_array($row->id, $regions)) {
                return [
                    "name" => $row->city,
                    "value" => $row->id,
                    "available" => true,
                    "sizes" => null
                ];
            }
        })->filter();

        return response()->json($filteredRegions);
    }

    /**
     * Get sizes for the given cloud provider.
     *
     * @param  CloudProvider  $cloudProvider
     * @return \Illuminate\Http\JsonResponse
     */
    public function sizes(CloudProvider $cloudProvider)
    {
        try {
            $user = auth()->user();

            if (!$region = request()->get('region')) {
                // ❌ Error response
                return response()->json([
                    'message' => "Invalid request!"
                ], 500);
            }

            $plans = $cloudProvider->plans()->whereRegion($region)->whereVisible(true)->get();

            if ($cloudProvider->provider == CloudProviderEnums::LIGHTSAIL()) {
                return $this->handleLightsailSizes($cloudProvider, $region, $plans);
            } elseif ($cloudProvider->provider == CloudProviderEnums::DIGITALOCEAN()) {
                return $this->handleDigitalOceanSizes($cloudProvider, $region, $plans);
            } elseif ($cloudProvider->provider == CloudProviderEnums::VULTR()) {
                return $this->handleVultrSizes($cloudProvider, $region, $plans);
            } elseif ($cloudProvider->provider == CloudProviderEnums::LINODE()) {
                return $this->handleLinodeSizes($cloudProvider, $region, $plans);
            } elseif ($cloudProvider->provider == CloudProviderEnums::HETZNER()) {
                return $this->handleHetznerSizes($cloudProvider, $region, $plans);
            }

            // ❌ Not Found
            return response()->json([
                'message' => "Server Provider not found!"
            ], 404);
        } catch (\Exception $e) {
            report($e);
            // ❌ Error response
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Handle sizes for Lightsail provider.
     *
     * @param  CloudProvider  $cloudProvider
     * @param  string  $region
     * @param  mixed  $plans
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleLightsailSizes(CloudProvider $cloudProvider, $region, $plans)
    {
        try {
            $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, $region);
            $data = $client->getBundles([
                'includeInactive' => false
            ]);

            $data = collect($data['bundles'])->filter(function ($row) {
                return in_array("LINUX_UNIX", $row['supportedPlatforms']);
            });

            $filteredSizes = $sizes = collect($data)->map(function ($row) use ($plans) {
                $serverPlan = collect($plans)->filter(function ($spRow) use ($row) {
                    return $spRow['size_slug'] == $row['bundleId'];
                })->first();

                if ($serverPlan) {
                    $sizeInMb = $row['ramSizeInGb'] * 1024;
                    return [
                        'name' => $row['name'],
                        'slug' => $row['bundleId'],
                        'ram_size_in_mb' => "{$sizeInMb}",
                        'cpu_core' => "{$row['cpuCount']}",
                        'disk_size_in_gb' => "{$row['diskSizeInGb']}",
                        'price' => $serverPlan['price_per_month'],
                        'bandwidth' => $serverPlan['bandwidth'],
                        'visible' => $serverPlan['visible'],
                    ];
                }
            });

            $finalSizes = [];
            foreach ($filteredSizes as $filteredSize) {
                if ($filteredSize) {
                    $finalSizes[] = $filteredSize;
                }
            }

            $data = collect($finalSizes)->groupBy('name');

            $sizes = $data->map(function ($row, $key) {
                return [
                    'name' => $key,
                    'list' => $row
                ];
            })->values();

            // ✅ Success response
            return response()->json([
                'sizes' => $sizes,
                'region' => $region
            ], 200);
        } catch (AwsException $aws) {
            report($aws);
            // ❌ Error response
            return response()->json([
                'message' => $aws->getAwsErrorMessage()
            ], 500);
        }
    }

    /**
     * Handle sizes for DigitalOcean provider.
     *
     * @param  CloudProvider  $cloudProvider
     * @param  string  $region
     * @param  mixed  $plans
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleDigitalOceanSizes(CloudProvider $cloudProvider, $region, $plans)
    {
        $data = Client::digitalOcean("sizes?per_page=80", "GET", $cloudProvider->access_key);
        $data = json_decode($data);
        $totalRecord = $data->meta->total;
        $perPage = 20;
        $requestSend = ceil($totalRecord / $perPage);
        $sizesArr = [];
        $page = 0;

        for ($i = 1; $i <= $requestSend; $i++) {
            $page++;
            $data1 = Client::digitalOcean("sizes?page=$page", "GET", $cloudProvider->access_key);
            if (isset($data1['error'])) {
                // ❌ Error response
                return response()->json([
                    'message' => $data1['message']
                ], 500);
            }
            $data1 = json_decode($data1);
            $sizesArr = array_merge($sizesArr, $data1->sizes);
        }

        $regionPlans = collect($sizesArr)->filter(function ($row) use ($region) {
            return in_array($region, $row->regions);
        });

        $filteredSizes = $sizes = collect($regionPlans)->map(function ($row) use ($plans) {
            $serverPlan = collect($plans)->filter(function ($spRow) use ($row) {
                return $spRow['size_slug'] == $row->slug;
            })->first();

            if ($serverPlan) {
                return [
                    'name' => $row->description,
                    'slug' => $row->slug,
                    'ram_size_in_mb' => "{$row->memory}",
                    'cpu_core' => "{$row->vcpus}",
                    'disk_size_in_gb' => "{$row->disk}",
                    'price' => $serverPlan['price_per_month'],
                    'bandwidth' => $serverPlan['bandwidth'],
                    'visible' => $serverPlan['visible'],
                ];
            }
        });

        $finalSizes = [];
        foreach ($filteredSizes as $filteredSize) {
            if ($filteredSize) {
                $finalSizes[] = $filteredSize;
            }
        }

        $data = collect($finalSizes)->groupBy('name');

        $sizes = $data->map(function ($row, $key) {
            return [
                'name' => $key,
                'list' => $row
            ];
        })->values();

        // ✅ Success response
        return response()->json([
            'sizes' => $sizes,
            'region' => $region
        ], 200);
    }

    /**
     * Handle sizes for Vultr provider.
     *
     * @param  CloudProvider  $cloudProvider
     * @param  string  $region
     * @param  mixed  $plans
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleVultrSizes(CloudProvider $cloudProvider, $region, $plans)
    {
        $data = Client::vultr("plans", "GET", $cloudProvider->access_key);
        if (isset($data['error'])) {
            // ❌ Error response
            return response()->json([
                'message' => $data['message']
            ], 500);
        }

        $data = json_decode($data);

        $regionPlans = collect($data->plans)->filter(function ($row) use ($region) {
            return in_array($region, $row->locations);
        });

        $filteredSizes = $sizes = collect($regionPlans)->map(function ($row) use ($plans) {
            $serverPlan = collect($plans)->filter(function ($spRow) use ($row) {
                return $spRow['size_slug'] == $row->id;
            })->first();

            if ($serverPlan) {
                if ($row->type == "vhf") {
                    $name = "High Frequency";
                } else if ($row->type == "vdc") {
                    $name = "Dedicated Cloud";
                } else {
                    $name = "Cloud Compute";
                }

                return [
                    'name' => $name,
                    'slug' => $row->id,
                    'ram_size_in_mb' => "{$row->ram}",
                    'cpu_core' => "{$row->vcpu_count}",
                    'disk_size_in_gb' => "{$row->disk}",
                    'price' => $serverPlan['price_per_month'],
                    'bandwidth' => $serverPlan['bandwidth'],
                    'visible' => $serverPlan['visible'],
                ];
            }
        });

        $finalSizes = [];
        foreach ($filteredSizes as $filteredSize) {
            if ($filteredSize) {
                $finalSizes[] = $filteredSize;
            }
        }

        $data = collect($finalSizes)->groupBy('name');

        $sizes = $data->map(function ($row, $key) {
            return [
                'name' => $key,
                'list' => $row
            ];
        })->values();

        // ✅ Success response
        return response()->json([
            'sizes' => $sizes,
            'region' => $region
        ], 200);
    }

    /**
     * Handle sizes for Linode provider.
     *
     * @param  CloudProvider  $cloudProvider
     * @param  string  $region
     * @param  mixed  $plans
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleLinodeSizes(CloudProvider $cloudProvider, $region, $plans)
    {
        $data = Client::linode("linode/types", "GET", $cloudProvider->access_key);
        if (isset($data['error'])) {
            // ❌ Error response
            return response()->json([
                'message' => $data['message']
            ], 500);
        }

        $data = json_decode($data);

        $filteredSizes = $sizes = collect($data->data)->map(function ($row) use ($plans) {
            $serverPlan = collect($plans)->filter(function ($spRow) use ($row) {
                return $spRow['size_slug'] == $row->id;
            })->first();

            if ($serverPlan) {
                if ($row->class == "standard" || $row->class == "nanode") {
                    $name = "Shared CPU";
                } else if ($row->class == "highmem") {
                    $name = "High Memory";
                } else if ($row->class == "dedicated") {
                    $name = "Dedicated CPU";
                } else {
                    $name = "GPU";
                }

                return [
                    'name' => $name,
                    'label' => $row->label,
                    'slug' => $row->id,
                    'ram_size_in_mb' => $row->memory,
                    'cpu_core' => $row->vcpus,
                    'disk_size_in_gb' => $row->disk / 1024,
                    'price' => $serverPlan['price_per_month'],
                    'bandwidth' => $serverPlan['bandwidth'],
                    'visible' => $serverPlan['visible'],
                ];
            }
        });

        $finalSizes = [];
        foreach ($filteredSizes as $filteredSize) {
            if ($filteredSize) {
                $finalSizes[] = $filteredSize;
            }
        }

        $data = collect($finalSizes)->groupBy('name');

        $sizes = $data->map(function ($row, $key) {
            return [
                'name' => $key,
                'list' => $row
            ];
        })->values();

        return response()->json([
            "sizes" => $sizes,
            "region" => $region
        ]);
    }

    /**
     * Handle sizes for Hetzner provider.
     *
     * @param  CloudProvider  $cloudProvider
     * @param  string  $region
     * @param  mixed  $plans
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleHetznerSizes(CloudProvider $cloudProvider, $region, $plans)
    {
        $data = Client::hetzner("server_types", "GET", $cloudProvider->access_key);
        if (isset($data['error'])) {
            // ❌ Error response
            return response()->json([
                'message' => $data['message']
            ], 500);
        }

        $data = json_decode($data);

        $filteredSizes = $sizes = collect($data->server_types)->map(function ($row) use ($plans, $region) {
            $serverPlan = collect($plans)->filter(function ($spRow) use ($row, $region) {
                return $spRow['size_slug'] == $row->id;
            })->first();

            if ($serverPlan) {
                if ($row->cpu_type == "shared") {
                    $name = "Standard";
                } else if ($row->cpu_type == "dedicated") {
                    $name = "Dedicated CPU";
                }

                $price = collect($row->prices)->where('location', $region)->values();

                return [
                    'name' => $name,
                    'label' => strtoupper($row->name),
                    'slug' => $row->id,
                    'ram_size_in_mb' => $row->memory * 1024,
                    'cpu_core' => $row->cores,
                    'disk_size_in_gb' => $row->disk,
                    'price' => $serverPlan['price_per_month'],
                    'bandwidth' => $serverPlan['bandwidth'],
                    'visible' => $serverPlan['visible'],
                ];
            }
        });

        $finalSizes = [];
        foreach ($filteredSizes as $filteredSize) {
            if ($filteredSize) {
                $finalSizes[] = $filteredSize;
            }
        }

        $data = collect($finalSizes)->where('name', '!=', "")->groupBy('name');

        $sizes = $data->map(function ($row, $key) {
            return [
                'name' => $key,
                'list' => $row->sortBy('price')->values()
            ];
        })->values();

        return response()->json([
            "sizes" => $sizes,
            "region" => $region
        ]);
    }
}
