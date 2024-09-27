<template>
  <div class="min-h-screen flex justify-center items-center">
    <div class="my-10">
      <p
        :class="[isLightColor ? 'text-custom-700' : 'text-custom-500']"
        class="text-xl font-cera-pro-bold leading-8"
      >
        404
      </p>
      <h1
        class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl"
      >
        Page not found
      </h1>
      <p class="mt-6 text-base leading-7 text-gray-600">
        Sorry, we couldn't find the page you're looking for.
      </p>
      <div class="mt-10">
        <button
          @click="$router.push({ name: 'dashboard' })"
          :class="[isLightColor ? 'text-custom-700' : 'text-custom-500']"
          class="text-base font-cera-pro-bold leading-7 cursor-pointer"
        >
          <span aria-hidden="true" class="text-2xl font-semibold">&larr;</span>
          Back to dashboard
        </button>
      </div>
    </div>
  </div>
</template>
