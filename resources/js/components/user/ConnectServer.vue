<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div v-if="providerList.length > 0">
    <h1 class="text-xl text-[#31363f] mb-3 font-medium">Create a Server</h1>
    <div
      v-if="user"
      :class="[user.email_verified_at === null ? 'mb-8 ' : 'mb-0']"
    >
      <span
        v-if="user.email_verified_at === null"
        class="font-medium text-tiny bg-yellow-100 text-yellow-600 p-2 px-3 rounded-md"
      >
        Note: Your email address has not been verified. Please verify your email
        to connect a server.
      </span>
    </div>
    <div class="relative">
      <div
        v-if="server.provider"
        class="absolute bg-gray-200 left-2 top-5 h-full w-[1px]"
        aria-hidden="true"
      ></div>
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          ' z-10 justify-center  pl-[3px]   rounded-full tabular-nums',
        ]"
      >
        <i class="fa-solid fa-circle text-[10px]"></i>
      </span>
      <div class="pl-8 -mt-5" v-if="user">
        <h1 class="font-medium mb-4">Provider Selection</h1>
        <RadioGroup
          v-model="server.provider"
          :disabled="user.email_verified_at === null"
        >
          <div
            class="grid 2xl:grid-cols-6 xl:grid-cols-5 sm:grid-cols-3 grid-cols-1 gap-5"
          >
            <RadioGroupOption
              as="template"
              v-for="provider in providerList"
              :key="provider.id"
              :value="provider.provider"
              v-slot="{ active, checked }"
              @click="fetchRegion"
              :disabled="user.email_verified_at === null"
            >
              <div
                :class="[
                  active || checked
                    ? isLightColor
                      ? 'border-custom-700'
                      : 'border-custom-500'
                    : '',
                  '  w-full overflow-hidden rounded-lg border relative ',
                  user.email_verified_at === null
                    ? 'opacity-50 cursor-not-allowed'
                    : 'cursor-pointer ',
                ]"
              >
                <RadioGroupLabel as="span" class="">
                  <div class="p-4 py-3">
                    <img
                      v-if="cloudLogos"
                      :src="cloudLogos[provider.provider].logo"
                      class="w-11 mx-auto h-auto"
                      :class="[
                        provider.provider === 'vultr'
                          ? '2xl:w-14 xl:w-12 xl:mb-1 2xl:mb-0'
                          : '',
                      ]"
                      alt=""
                    />
                    <span
                      class="capitalize justify-center font-medium text-[16px] items-center flex py-3 pb-6"
                      >{{ cloudLogos[provider.provider].title }}</span
                    >
                  </div>
                  <div
                    :class="` absolute bottom-0 w-full flex items-center p-1 justify-center ${
                      checked
                        ? isLightColor
                          ? 'bg-custom-700 text-black'
                          : 'bg-custom-500 text-white'
                        : 'bg-[#F5F7F8] text-gray-400'
                    }`"
                  >
                    <span class="material-symbols-outlined text-[18px]">
                      {{
                        checked
                          ? "radio_button_checked"
                          : "radio_button_unchecked"
                      }}
                    </span>
                  </div>
                </RadioGroupLabel>
              </div>
            </RadioGroupOption>
          </div>
        </RadioGroup>
        <small
          id="provider_message"
          class="error_message text-red-500 text-xs"
        ></small>
      </div>
    </div>
    <div
      class="relative my-5"
      v-if="server.provider && providerList.length > 0"
    >
      <div
        class="absolute bg-gray-200 left-2 top-5 h-full w-[1px]"
        aria-hidden="true"
      ></div>
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          ' z-10 justify-center  pl-[3px]   rounded-full tabular-nums',
        ]"
      >
        <i class="fa-solid fa-circle text-[10px]"></i>
      </span>
      <div class="pl-8 -mt-5">
        <h1 class="font-medium">OS Version</h1>
        <RadioGroup v-model="server.version">
          <div
            class="mt-5 grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 grid-cols-1 gap-5"
          >
            <RadioGroupOption
              as="template"
              v-for="version in selectVersion"
              :key="version.id"
              :value="version.value"
              v-slot="{ active, checked }"
              :disabled="
                version.value == '24' && server.provider == 'lightsail'
              "
            >
              <div
                :class="[
                  active || checked
                    ? isLightColor
                      ? 'border-custom-700'
                      : 'border-custom-500'
                    : '',
                  'relative flex gap-2  overflow-hidden rounded-lg border ',
                  version.value == '24' && server.provider == 'lightsail'
                    ? 'cursor-not-allowed opacity-50'
                    : 'cursor-pointer',
                ]"
              >
                <div
                  :class="` absolute top-1 right-1  flex items-center p-1 justify-center ${
                    checked
                      ? isLightColor
                        ? 'text-custom-700'
                        : 'text-custom-500'
                      : ' text-gray-400'
                  }`"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    {{
                      checked
                        ? "radio_button_checked"
                        : "radio_button_unchecked"
                    }}
                  </span>
                </div>
                <RadioGroupLabel as="span" class="flex">
                  <div class="flex items-center gap-4 p-4">
                    <div class="max-w-8 max-h-8">
                      <img
                        :src="`/logo/ubuntu.svg`"
                        alt=""
                        class="w-full h-full"
                      />
                    </div>
                    <span class="text-tiny font-medium">{{
                      version.title
                    }}</span>
                  </div>
                </RadioGroupLabel>
              </div>
            </RadioGroupOption>
          </div>
        </RadioGroup>
        <small
          id="version_message"
          class="text-red-500 error_message text-xs"
        ></small>
      </div>
    </div>
    <div
      class="relative my-5"
      v-if="server.provider && providerList.length > 0"
    >
      <div
        class="absolute bg-gray-200 left-2 top-5 h-full w-[1px]"
        aria-hidden="true"
      ></div>
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          ' z-10 justify-center  pl-[3px]   rounded-full tabular-nums',
        ]"
      >
        <i class="fa-solid fa-circle text-[10px]"></i>
      </span>
      <div class="pl-8 -mt-5" v-if="server.provider && providerList.length > 0">
        <h1 class="font-medium">Tech Stack</h1>
        <RadioGroup v-model="server.web_server">
          <div
            class="mt-5 grid sm:grid-cols-2 xl:grid-cols-4 grid-cols-1 gap-5"
          >
            <RadioGroupOption
              as="template"
              v-for="service in services"
              :key="service.id"
              :value="service.value"
              v-slot="{ active, checked }"
            >
              <div
                :class="[
                  active || checked
                    ? isLightColor
                      ? 'border-custom-700'
                      : 'border-custom-500'
                    : '',
                  'relative flex gap-2 cursor-pointer overflow-hidden rounded-lg border ',
                ]"
              >
                <div
                  :class="` absolute top-1 right-1  flex items-center p-1 justify-center ${
                    checked
                      ? isLightColor
                        ? 'text-custom-700'
                        : 'text-custom-500'
                      : ' text-gray-400'
                  }`"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    {{
                      checked
                        ? "radio_button_checked"
                        : "radio_button_unchecked"
                    }}
                  </span>
                </div>
                <RadioGroupLabel as="span" class="flex">
                  <div class="">
                    <div
                      class="flex justify-center items-center text-center 2xl:w-28 xs:w-24 xl:w-[65px] w-[70px] h-full bg-[#F5F7F8] p-5 px-2"
                    >
                      <div class="relative flex flex-col">
                        <img
                          :src="service.img"
                          alt="no img"
                          class="w-10 h-10"
                          v-if="service.title === 'Apache'"
                        />
                        <img
                          :src="service.img"
                          alt="no img"
                          class="w-9 h-9"
                          v-else-if="service.title === 'Nginx'"
                        />
                        <img
                          :src="service.img"
                          alt="no img"
                          class="h-10 w-10"
                          v-else-if="service.title === 'OLS'"
                        />
                        <img
                          :src="service.img"
                          alt="no img"
                          class="h-4 2xl:h-5"
                          v-else-if="service.title === 'NodeJs'"
                        />
                        <span
                          class="text-[11px] xs:text-[12px] 2xl:text-sm font-medium mt-3"
                        >
                          {{ service.title }}
                        </span>
                        <p
                          v-if="service.title === 'NodeJs'"
                          class="text-blue-500 absolute 2xl:-top-4 -top-2.5 font-medium -right-0 2xl:text-[11px] xl:text-[8px] text-[10px]"
                        >
                          BETA
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="py-3 px-2 flex-1">
                    <div
                      class="text-gray-600 pt-1.5"
                      v-if="
                        service.title === 'Apache' ||
                        service.title === 'NodeJs' ||
                        service.title === 'OLS'
                      "
                    >
                      <div
                        v-if="service.title === 'Apache'"
                        class="px-1 py-2.5 text-[11px] xs:text-[12px] xl:text-[11px] 2xl:text-[12px]"
                      >
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>PHP (From 7.2 to 8.3)</span>
                        </div>
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>PHP-FPM Configuration</span>
                        </div>
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>MySQL/MariaDB</span>
                        </div>
                        <div class="pt-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>Redis</span>
                        </div>
                      </div>
                      <div
                        v-if="service.title === 'NodeJs'"
                        class="px-1 py-2.5 text-[11px] xs:text-[12px] xl:text-[11px] 2xl:text-[12px] text-gray-600"
                      >
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>Node Stack</span>
                        </div>
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>Nginx</span>
                        </div>
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>MongoDB</span>
                        </div>
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>Redis</span>
                        </div>
                      </div>
                      <div
                        v-if="service.title === 'OLS'"
                        class="px-1 py-2.5 text-[11px] xs:text-[12px] xl:text-[11px] 2xl:text-[12px] text-gray-600"
                      >
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>Openlitespeed</span>
                        </div>
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>LSPHP (From 7.2 to 8.1)</span>
                        </div>
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>MySQL/MariaDB</span>
                        </div>
                        <div class="py-1 flex 2xl:items-center gap-2">
                          <i
                            class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                          ></i>
                          <span>Redis</span>
                        </div>
                      </div>
                    </div>
                    <div
                      v-if="service.title === 'Nginx'"
                      class="px-1 py-3 text-[11px] xs:text-[12px] xl:text-[11px] 2xl:text-[12px] text-gray-600"
                    >
                      <div class="py-1 flex 2xl:items-center gap-2">
                        <i
                          class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                        ></i>
                        <span>PHP (From 7.2 to 8.3)</span>
                      </div>
                      <div class="py-1 flex 2xl:items-center gap-2">
                        <i
                          class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                        ></i>
                        <span>PHP-FPM Configuration</span>
                      </div>
                      <div class="py-1 flex 2xl:items-center gap-2">
                        <i
                          class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                        ></i>
                        <span>MySQL/MariaDB</span>
                      </div>
                      <div class="py-1 flex 2xl:items-center gap-2">
                        <i
                          class="fa-solid fa-circle text-[5px] 2xl:pt-0 pt-1.5"
                        ></i>
                        <span>Redis</span>
                      </div>
                    </div>
                  </div>
                </RadioGroupLabel>
              </div>
            </RadioGroupOption>
          </div>
        </RadioGroup>
        <small
          id="web_server_message"
          class="error_message text-red-500 text-xs"
        ></small>

        <div class="my-5">
          <div
            v-if="server.web_server !== 'mern'"
            class="2xl:cols-sapn-12 xl:col-span-8 md:col-span-7 sm:col-span-8 col-span-12"
          >
            <div
              class="sm:items-center items-center gap-5 bg-gray-50 p-4 rounded-md border border-primary shadow-sm"
            >
              <div class="flex items-center justify-between">
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Install Node.js</label
                >
                <Switch
                  v-model="server.nodejs"
                  :class="[
                    server.nodejs
                      ? isLightColor
                        ? 'bg-custom-700'
                        : 'bg-custom-500'
                      : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
                  ]"
                >
                  <span class="sr-only">Use setting</span>
                  <span
                    aria-hidden="true"
                    :class="[
                      server.nodejs ? 'translate-x-5' : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    ]"
                  />
                </Switch>
              </div>
              <p class="text-gray-500 text-sm md:mt-1 md:w-[85%] mt-3">
                When you activate the toggle button, it will automatically
                handle the installation of Node on your server, simplifying the
                setup process.
              </p>
            </div>
            <small
              id="nodejs_message"
              class="error_message text-red-500 text-xs"
            ></small>
          </div>
          <div
            v-else
            class="2xl:cols-sapn-12 xl:col-span-8 md:col-span-7 sm:col-span-8 col-span-12"
          >
            <div
              class="sm:items-center items-center gap-5 bg-gray-50 p-4 rounded-md border border-primary shadow-sm"
            >
              <div class="flex items-center justify-between">
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Install Yarn</label
                >
                <Switch
                  v-model="server.yarn"
                  :class="[
                    server.yarn
                      ? isLightColor
                        ? 'bg-custom-700'
                        : 'bg-custom-500'
                      : 'bg-gray-200',
                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
                  ]"
                >
                  <span class="sr-only">Use setting</span>
                  <span
                    aria-hidden="true"
                    :class="[
                      server.yarn ? 'translate-x-5' : 'translate-x-0',
                      'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    ]"
                  />
                </Switch>
              </div>
              <p class="text-gray-500 text-sm md:mt-1 md:w-[85%] mt-3">
                When you activate the toggle button, it will automatically
                handle the installation of Yarn on your server, simplifying the
                setup process.
              </p>
            </div>
            <small
              id="yarn_message"
              class="error_message text-red-500 text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="relative my-5"
      v-if="server.provider && providerList.length > 0"
    >
      <div
        class="absolute bg-gray-200 left-2 top-5 h-full w-[1px]"
        aria-hidden="true"
      ></div>
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          ' z-10 justify-center  pl-[3px]   rounded-full tabular-nums',
        ]"
      >
        <i class="fa-solid fa-circle text-[10px]"></i>
      </span>
      <div class="pl-8 -mt-5" v-if="server.provider && providerList.length > 0">
        <h1 class="font-medium">Database</h1>
        <RadioGroup v-model="server.database_type">
          <div
            class="mt-5 grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 grid-cols-1 gap-5"
          >
            <RadioGroupOption
              as="template"
              v-for="data in database"
              :key="data.id"
              :value="data.value"
              v-slot="{ active, checked }"
              :disabled="showDb(data.value)"
            >
              <div
                :class="[
                  active || checked
                    ? isLightColor
                      ? 'border-custom-700'
                      : 'border-custom-500'
                    : '',
                  'relative flex gap-2  overflow-hidden rounded-lg border ',
                  showDb(data.value)
                    ? 'cursor-not-allowed opacity-50'
                    : 'cursor-pointer',
                ]"
              >
                <div
                  :class="` absolute top-1 right-1  flex items-center p-1 justify-center ${
                    checked
                      ? isLightColor
                        ? 'text-custom-700'
                        : 'text-custom-500'
                      : ' text-gray-400'
                  }`"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    {{
                      checked
                        ? "radio_button_checked"
                        : "radio_button_unchecked"
                    }}
                  </span>
                </div>
                <RadioGroupLabel as="span" class="flex justify-center w-full">
                  <div class="flex justify-center items-center gap-4 p-4">
                    <img :src="data.img" alt="" :class="data.classes" />
                  </div>
                </RadioGroupLabel>
              </div>
            </RadioGroupOption>
          </div>
        </RadioGroup>
        <small
          id="database_type_message"
          class="text-red-500 error_message text-xs"
        ></small>
      </div>
    </div>
    <div
      class="relative my-5"
      v-if="server.provider && providerList.length > 0"
    >
      <div
        v-if="server.region"
        class="absolute bg-gray-200 left-2 top-5 h-full w-[1px]"
        aria-hidden="true"
      ></div>
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          ' z-10 justify-center  pl-[3px]   rounded-full tabular-nums',
        ]"
      >
        <i class="fa-solid fa-circle text-[10px]"></i>
      </span>
      <div class="pl-8 -mt-5">
        <h1 class="font-medium">Location</h1>
        <div
          v-if="server.provider && providerList.length > 0"
          class="grid sm:grid-cols-2 grid-cols-1 mt-2 xl:mt-4 xl:gap-x-20 md:gap-x-10 gap-x-5 gap-3"
        >
          <div>
            <div
              class="grid grid-cols-12 xl:grid-cols-8 2xl:grid-cols-12 sm:gap-x-3 gap-1"
            >
              <div class="2xl:col-span-3 xl:col-span-2 col-span-12 xl:pt-2">
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Location</label
                >
              </div>
              <div class="xl:col-span-6 2xl:col-span-9 col-span-12">
                <div v-if="server.provider">
                  <select
                    v-model="server.region"
                    id="region"
                    name="region"
                    class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                  >
                    <option :value="null" selected>
                      Select a Server Location
                    </option>
                    <option
                      v-for="region in selectRegion"
                      :key="region"
                      :value="region.value"
                    >
                      {{ region.name }}
                    </option>
                  </select>
                  <small
                    id="region_message"
                    class="error_message text-red-500 text-xs"
                  ></small>
                </div>
              </div>
            </div>
          </div>
          <div>
            <div
              v-if="selectPlan.length > 0 && server.provider === 'lightsail'"
              class="grid grid-cols-12 xl:grid-cols-9 2xl:grid-cols-12 sm:gap-x-3 gap-1"
            >
              <div class="xl:col-span-3 col-span-12 xl:pt-2">
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Availability Zone</label
                >
              </div>
              <div class="xl:col-span-6 2xl:col-span-9 col-span-12">
                <div v-if="server.provider">
                  <select
                    v-model="server.availabilityZone"
                    id="availabilityZone"
                    name="location"
                    class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                  >
                    <option :value="null" selected>
                      Select a Availability Zone
                    </option>
                    <option
                      v-for="zone in selectAvailability"
                      :key="zone"
                      :value="zone.zoneName"
                    >
                      {{ zone.zoneName }}
                    </option>
                  </select>
                  <small
                    id="availabilityZone_message"
                    class="error_message text-red-500 text-xs"
                  ></small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="relative my-5"
      v-if="server.region !== null && selectPlan.length > 0"
    >
      <div
        class="absolute bg-gray-200 left-2 top-5 h-full w-[1px]"
        aria-hidden="true"
      ></div>
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          ' z-50 justify-center  pl-[3px]   rounded-full  tabular-nums',
        ]"
      >
        <i class="fa-solid fa-circle text-[10px] z-50"></i>
      </span>
      <div class="pl-8 -mt-5">
        <h1 class="font-medium mb-4">Plan Selection</h1>
        <div class="xl:grid xl:grid-cols-10 2xl:grid-cols-11 grid-col-12 gap-4">
          <div class="xl:col-span-2 col-span-12">
            <div class="shadow rounded-md">
              <h1
                class="bg-[#F6F6F6] p-4 xl:text-center text-left rounded-t-md font-medium text-[15px]"
              >
                Select Server Plans
              </h1>
              <perfectScrollbar
                v-if="server.region !== null && selectPlan.length > 0"
                class="max-h-[30rem]"
              >
                <RadioGroup v-model="server.plan">
                  <div class="xl:px-3 xl:py-1.5 p-1.5">
                    <RadioGroupOption
                      as="template"
                      v-for="plan in selectPlan"
                      :key="plan"
                      :value="plan.name"
                      v-slot="{ active, checked }"
                    >
                      <div
                        :class="[
                          active || checked
                            ? isLightColor
                              ? 'bg-custom-200 text-custom-700 border-0'
                              : 'text-custom-500 bg-custom-50 border-0'
                            : '',
                          'inline-flex xl:flex  items-center  cursor-pointer xl:m-0 m-2   xl:my-2  rounded-lg border',
                        ]"
                      >
                        <div
                          :class="`  inline-flex items-center gap-3  p-2  ${
                            checked
                              ? isLightColor
                                ? 'text-custom-700'
                                : 'text-custom-500'
                              : ' text-gray-400 '
                          }`"
                        >
                          <span class="material-symbols-outlined text-[18px]">
                            {{
                              checked
                                ? "radio_button_checked"
                                : "radio_button_unchecked"
                            }}
                          </span>
                          <spab class="text-tiny">
                            {{ plan.name }}
                          </spab>
                        </div>
                      </div>
                    </RadioGroupOption>
                  </div>
                </RadioGroup>
              </perfectScrollbar>
            </div>
            <small
              id="plan_message"
              class="error_message text-red-500 text-xs"
            ></small>
          </div>
          <div class="xl:col-span-8 2xl:col-span-9 col-span-12 mt-5 xl:mt-0">
            <div v-if="newPlanList.length > 0">
              <Table
                :bodyHeight="'max-h-[30rem]'"
                :head="thead"
                v-if="newPlanList[0] && newPlanList[0].list.length > 0"
              >
                <tr
                  :class="[
                    'text-[#2c3138] text-sm',
                    index !== 0 ? 'border-t border-primary' : '',
                  ]"
                  v-for="(list, index) in newPlanList[0].list"
                  :key="list.slug"
                >
                  <td
                    class="whitespace-nowrap py-5 px-4 pl-6 truncate"
                    v-if="
                      server.provider == 'hetzner' ||
                      server.provider == 'linode'
                    "
                  >
                    {{ list.label ? list.label : "-" }}
                  </td>
                  <td class="whitespace-nowrap pl-6 px-4 py-4">
                    {{ list.cpu_core }}
                  </td>
                  <td class="whitespace-nowrap px-4 py-4">
                    {{ list.ram_size_in_mb }} MB
                  </td>
                  <td class="whitespace-nowrap px-4 py-4">
                    {{ list.disk_size_in_gb }} GB
                  </td>
                  <td class="whitespace-nowrap px-4 py-4">
                    {{ list.bandwidth }} GB
                  </td>
                  <td class="whitespace-nowrap px-4 py-4">
                    {{ formatCurrency(list.price) }}
                  </td>
                  <td class="text-center px-4 py-4">
                    <RadioGroup v-model="server.sizeSlug">
                      <RadioGroupOption
                        as="template"
                        :value="list.slug"
                        v-slot="{ checked }"
                      >
                        <div
                          :class="[
                            server.sizeSlug && checked
                              ? isLightColor
                                ? 'bg-custom-700 text-white'
                                : 'bg-custom-500 text-white'
                              : 'bg-gray-200 text-gray-900',
                            'rounded-md  mx-auto py-2 px-2 max-w-20 shadow-sm focus:outline-none cursor-pointer',
                          ]"
                        >
                          <RadioGroupLabel
                            as="span"
                            class="inline-block text-sm font-medium"
                          >
                            <span>{{
                              server.sizeSlug && checked ? "Selected" : "Select"
                            }}</span>
                          </RadioGroupLabel>
                          <span
                            :class="[
                              'border',
                              checked
                                ? isLightColor
                                  ? 'border-custom-700'
                                  : 'border-custom-500'
                                : 'border-transparent',
                              'pointer-events-none absolute -inset-px rounded',
                            ]"
                            aria-hidden="true"
                          />
                        </div>
                      </RadioGroupOption>
                    </RadioGroup>
                  </td>
                </tr>
              </Table>
              <template v-else>
                <TableSkeleton :heads="6" v-if="refreshing" />
                <Table :head="thead" v-else>
                  <tr>
                    <td
                      colspan="6"
                      class="text-center text-sm whitespace-nowrap py-4 px-4"
                    >
                      {{ refreshing ? "Please Wait" : "No Data Found" }}
                    </td>
                  </tr>
                </Table>
              </template>
              <small
                id="sizeSlug_message"
                class="error_message text-red-500 text-xs"
              ></small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="relative my-5"
      v-if="server.region !== null && selectPlan.length > 0"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          ' z-10 justify-center  pl-[3px]   rounded-full tabular-nums',
        ]"
      >
        <i class="fa-solid fa-circle text-[10px]"></i>
      </span>
      <div class="pl-8 -mt-5" v-if="server.provider && providerList.length > 0">
        <h1 class="font-medium">Basic Server Information</h1>
        <div
          v-if="server.provider && providerList.length > 0"
          class="grid sm:grid-cols-2 grid-cols-1 md:mt-2 xl:mt-4 xl:gap-x-20 md:gap-x-10 gap-x-5 gap-2"
        >
          <div>
            <div
              class="grid grid-cols-12 2xl:grid-cols-12 xl:grid-cols-8 sm:gap-x-3 gap-1"
            >
              <div class="xl:col-span-2 2xl:col-span-3 col-span-12 xl:pt-2">
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Server Name</label
                >
              </div>

              <div class="xl:col-span-6 2xl:col-span-9 col-span-12">
                <input
                  v-model="server.name"
                  type="text"
                  name="server"
                  id="name"
                  class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                  placeholder="Enter Server Name"
                />

                <small
                  id="name_message"
                  class="error_message text-red-500 text-xs"
                ></small>
              </div>
            </div>
          </div>
          <div>
            <div
              v-if="selectPlan.length > 0 && server.provider === 'linode'"
              class="grid grid-cols-12 2xl:grid-cols-12 xl:grid-cols-9 sm:gap-x-3 gap-1"
            >
              <div class="xl:col-span-3 col-span-12 xl:pt-2">
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Root Password</label
                >
              </div>
              <div class="xl:col-span-6 2xl:col-span-9 col-span-12">
                <div>
                  <div class="relative">
                    <input
                      id="password"
                      name="password"
                      :type="showPassword ? 'text' : 'password'"
                      placeholder="Pswd@135#79"
                      v-model="server.linode_root_password"
                      :class="{ 'tracking-widest': !showPassword }"
                      class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                    />
                    <PasswordVisibility
                      :showPassword="showPassword"
                      @toggle="showPassword = !showPassword"
                    />
                  </div>
                </div>
                <p class="text-[11px] text-gray-700">
                  Note: Create a strong password with a mix of uppercase and
                  lowercase letters, numbers, and symbols. Avoid dictionary
                  words, repeated characters (aaa), sequences (abcd), and common
                  patterns (qwerty).
                </p>
                <small
                  id="linode_root_password_message"
                  class="text-red-500 error_message text-xs"
                ></small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="my-5 flex justify-end"
      v-if="server.provider && providerList.length > 0"
    >
      <div v-if="user">
        <div
          v-if="user.email_verified_at === null"
          v-tooltip="
            'Your email address has not been verified. Please verify your email to connect a server.'
          "
        >
          <Button :disabled="user.email_verified_at === null">
            <i
              v-if="processing"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{ processing ? "Please wait" : "Connect Now" }}
          </Button>
        </div>
        <Button :disabled="processing" @click="createServer" v-else>
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{ processing ? "Please wait" : "Connect Now" }}
        </Button>
      </div>
    </div>
  </div>
  <template v-else>
    <CloudPlatformInfo v-if="!fetchingProviders" />
  </template>
</template>

<script>
import {
  RadioGroup,
  RadioGroupDescription,
  RadioGroupLabel,
  RadioGroupOption,
  Switch,
} from "@headlessui/vue";
import { CheckCircleIcon } from "@heroicons/vue/20/solid";
import CloudLogo from "@/CloudProviders.js";
import Service from "@/Services.js";
import { mapState } from "pinia";
import { useAuthStore } from "@/store/auth";
import CloudPlatformInfo from "@/components/CloudPlatformInfo.vue";
export default {
  components: {
    RadioGroup,
    RadioGroupLabel,
    RadioGroupDescription,
    RadioGroupOption,
    CheckCircleIcon,
    Switch,
    CloudPlatformInfo,
  },
  computed: {
    ...mapState(useAuthStore, ["user"]),
  },
  data() {
    return {
      breadcrumb: {
        title: "Servers",
        icon: "dns",
        pages: [{ name: "Create" }],
      },
      server: {
        provider: "",
        region: null,
        plan: null,
        database_type: "mysql",
        web_server: "apache2",
        sizeSlug: "",
        nodejs: false,
        price: "",
        name: "",
        availabilityZone: "",
        linode_root_password: "",
        version: "22",
        ssh_key: 0,
        add_public_key: "",
      },
      fetchingProviders: false,
      processing: false,
      showPassword: false,
      cloudLogos: CloudLogo,
      Services: Service,
      refreshing: false,
      pagination: null,
      providerId: null,
      providerList: [],
      selectRegion: [],
      selectPlan: [],
      newPlanList: [],
      fetchingPlan: false,
      selectAvailability: [],
      thead: [
        {
          title: "Core(s)",
          classes: "pl-6",
        },
        {
          title: "Memory",
          classes: "",
        },
        {
          title: "Storage",
          classes: "",
        },
        {
          title: "Bandwith",
          classes: "",
        },
        {
          title: "Price per Month",
          classes: "",
        },
        {
          title: "  ",
        },
      ],
      selectVersion: [
        { title: "Ubuntu 20.04", value: "20" },
        { title: "Ubuntu 22.04", value: "22" },
        { title: "Ubuntu 24.04", value: "24" },
      ],
      services: [
        {
          id: 1,
          title: "Apache",
          value: "apache2",
          img: "/service/apache.svg",
        },
        {
          id: 2,
          title: "Nginx",
          value: "nginx",
          img: "/service/nginx.svg",
        },
        {
          id: 3,
          title: "OLS",
          value: "openlitespeed",
          img: "/logo/ols.svg",
        },
        {
          id: 4,
          title: "NodeJs",
          img: "/logo/node.svg",
          value: "mern",
        },
      ],
      database: [
        {
          id: 1,
          title: "MySQL",
          value: "mysql",
          img: "/service/mysql.webp",
          classes: "w-16 h-12",
        },
        {
          id: 2,
          title: "MariaDB",
          value: "mariadb",
          img: "/service/mariadb-logo.png",
          classes: " h-8",
        },
        {
          id: 3,
          title: "MongoDB",
          value: "mongodb",
          img: "/service/mongo.png",
          classes: "h-8",
        },
      ],
    };
  },

  watch: {
    "server.plan"(Val) {
      if (Val) {
        this.server.sizeSlug = "";
        let list = this.selectPlan.filter((name) => {
          return name.name === Val;
        });
        this.newPlanList = list;
        this.server.price = list[0].price;
      }
    },
    "server.web_server": {
      handler(val) {
        if (val == "mern") {
          this.server.database_type = "mongodb";
          this.server.nodejs = true;
        } else if (val !== "mern") {
          if(this.server.database_type == "mongodb"){
            this.server.database_type = "mysql";
          }
          this.server.yarn = false;
        }
      },
    },

    "server.provider"(Val) {
      if(Val == "lightsail" && this.server.version == "24"){
        this.server.version = "22";
      }
      this.server.region = null;
      this.providerList.find((provider) => {
        if (provider.provider === Val) {
          this.selectPlan = [];
          this.selectAvailability = [];
          this.newPlanList = [];
          this.providerId = provider.id;
        }
      });
      this.setTableHeader();
    },

    "server.region"(Val) {
      this.server.plan = null;
      this.selectPlan = [];
      this.newPlanList = [];
      this.selectAvailability = [];
      this.server.availabilityZone = null;

      if (this.server.provider === "lightsail" && Val) {
        this.fetchAvailabillity();
        this.fetchPlan();
      } else if (Val) {
        this.fetchPlan();
      }
    },
    "server.sizeSlug"(Val) {
      this.selectPlan.find((plan) => {
        if (plan.name === this.server.plan) {
          plan.list.find((value) => {
            if (value.slug === Val) {
              this.server.price = value.price;
            }
          });
        }
      });
    },
  },

  async mounted() {
    await this.getProviders();
  },
  methods: {
    setTableHeader() {
      if (
        this.server.provider == "hetzner" ||
        this.server.provider == "linode"
      ) {
        let isAlreadyAdded = this.thead.find(
          (column) => column.title === "Name"
        );
        if (!isAlreadyAdded) {
          this.thead.splice(0, 0, { title: "Name", classes: "pl-6" });
        }
      } else {
        this.thead = this.thead.filter((column) => column.title !== "Name");
      }
    },
    showDb(provider) {
      if (this.server.web_server != "") {
        if (
          (provider == "mysql" || provider == "mariadb") &&
          this.server.web_server == "mern"
        ) {
          return true;
        } else if (provider == "mongodb" && this.server.web_server !== "mern") {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    },
    async getProviders() {
      this.fetchingProviders = true;
      await this.$axios
        .get("/cloud-providers")
        .then(({ data }) => {
          this.providerList = data.cloud_providers;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.fetchingProviders = false;
        });
    },
    async fetchRegion() {
      if (this.user.email_verified_at === null) {
        return; // Do nothing if the user is not verified
      }
      this.refreshing = true;
      const loader = this.$loading.show();
      await this.$axios
        .get(`/cloud-providers/${this.providerId}/regions`)
        .then(({ data }) => {
          this.selectRegion = data;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          if (loader) {
            loader.hide();
          }
        });
    },

    async fetchPlan() {
      this.refreshing = true;
      this.fetchingPlan = true;
      const loader = this.$loading.show();
      await this.$axios
        .get(
          `/cloud-providers/${this.providerId}/sizes?region=${this.server.region}`
        )
        .then(({ data }) => {
          this.selectPlan = data.sizes;
          if (data.sizes.length > 0) {
            this.server.plan = data.sizes[0].name;
            this.server.price = data.sizes[0].price;
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          if (loader) {
            loader.hide();
          }
          this.fetchingPlan = false;
          this.refreshing = false;
        });
    },
    async fetchAvailabillity() {
      await this.$axios
        .get(
          `/cloud-providers/${this.providerId}/regions?region=${this.server.region}`
        )
        .then(({ data }) => {
          this.selectAvailability = data.region_zones;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async createServer() {
      this.processing = true;
      this.hideError();
      await this.$axios
        .post("/servers", this.server)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.$router.push({
            name: "InstallationStatus",
            params: {
              server: data.server.id,
            },
          });
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(response.data.message);
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>