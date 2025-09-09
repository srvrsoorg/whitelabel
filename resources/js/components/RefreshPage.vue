<script setup>
import { ref } from "vue"
import { watch } from "vue";
import { useRegisterSW } from "virtual:pwa-register/vue";

const { offlineReady, needRefresh, updateServiceWorker } = useRegisterSW();

const dismissedTemporarily = ref(false)

const checkDismissState = () => {
  const dismissUntil = parseInt(sessionStorage.getItem("pwa_dismiss_until") || "0", 10)
  const now = Date.now()

  if (now < dismissUntil) {
    dismissedTemporarily.value = true   
  } else {
    sessionStorage.removeItem("pwa_dismiss_until")
    dismissedTemporarily.value = false 
  }
}

checkDismissState()

watch(needRefresh, (val) => {
  if (val) checkDismissState()
})

const dismissTemporarily = () => {
  const until = Date.now() + 5 * 60 * 1000
  sessionStorage.setItem('pwa_dismiss_until', until.toString())
  dismissedTemporarily.value = true

  setTimeout(() => {
    checkDismissState()
  }, until - Date.now())
}

const reloadPage = async () => {
    offlineReady.value = false;
    needRefresh.value = false;
    updateServiceWorker(true);
    location.reload();
};
</script>

<template>
    <div
        v-if="needRefresh  && !dismissedTemporarily "
        class="fixed right-0 bottom-0 pointer-events-auto m-4 p-3 z-[60] text-left w-full sm:max-w-md max-w-[18rem] overflow-hidden rounded-lg bg-white shadow-lg border"
        role="alert"
    >
        <div class="flex items-start">
            <div class="shrink-0">
                <span class="material-symbols-outlined text-gray-900 text-xl">
                    autorenew
                </span>
            </div>
            <div class="ml-2.5 flex-1 mt-0.5">
                <div class="mb-1.5">
                    <p class="text-md text-gray-900">
                        Update Available
                    </p>
                    <p class="mt-1 text-tiny text-gray-500">
                        A new update is available. Please click the reload button to update.
                    </p>
                </div>
                <div class="mt-2 flex space-x-7">
                    <button
                        @click="reloadPage"
                        class="rounded-md text-tiny text-custom-500 focus:outline-hidden"
                    >
                        Reload
                    </button>
                </div>
            </div>
            <div class="ml-4 flex shrink-0">
                <button
                    type="button"
                    v-tooltip="
                        'Temporarily hides this update notice. It will reappear in 5 minutes.'
                    "
                    @click="dismissTemporarily"
                    class="inline-flex rounded-md text-gray-600  focus:ring-0 focus:ring-offset-0 focus:outline-hidden"
                >
                    <span class="material-symbols-outlined text-xl">
                        close
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
.pwa-toast {
    position: fixed;
    right: 0;
    bottom: 0;
    margin: 16px;
    padding: 12px;
    border: 1px solid #8885;
    border-radius: 4px;
    z-index: 1;
    text-align: left;
    background-color: white;
}
.pwa-toast .message {
    margin-bottom: 8px;
}
.pwa-toast button {
    border: 1px solid #8885;
    outline: none;
    margin-right: 5px;
    border-radius: 2px;
    padding: 3px 10px;
}
</style>