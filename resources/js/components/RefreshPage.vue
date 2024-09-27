<script setup>
import { useRegisterSW } from 'virtual:pwa-register/vue'

const {
  offlineReady,
  needRefresh,
  updateServiceWorker
} = useRegisterSW()

const reloadPage = async () => {
  offlineReady.value = false
  needRefresh.value = false
  updateServiceWorker(true);
  location.reload()
}

</script>

<template>
  <div
    v-if="needRefresh"
    class="pwa-toast shadow !z-[60]"
    role="alert"
  >
    <div class="message">
      <span>
        A new update is available. Please click the reload button to update.
      </span>
    </div>
    <button v-if="needRefresh" @click="reloadPage">
      Reload
    </button>
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