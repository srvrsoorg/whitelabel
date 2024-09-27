<template>
  <div class="bg-white flex flex-col h-full min-h-screen">
    <div class="flex flex-1 items-center justify-center h-full">
      <div
        class="p-5 w-full max-w-lg bg-white rounded-lg border border-gray-200 shadow-md sm:p-6 md:p-8"
      >
        <div class="flex justify-center items-center">
          <div>
            <div class="mb-5 text-center">
              <img
                src="/images/object.png"
                alt="ServerAvatar"
                width="200"
                class="mx-auto"
              />
            </div>
            <p class="mt-8 mb-5 text-xl font-bold text-center">
              Verification ongoing, making progress!
            </p>
            <p class="mb-5 text-center text-gray-700">
              Thank you for your patience as we verify your account and set up
              logins.
            </p>
            <p class="text-center text-gray-700">
              This process may require a few moments.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { useAuthStore } from "@/store/auth";
import { mapState, mapActions } from "pinia";
export default {
  name: "UserVerification",
  data() {
    return {};
  },
  computed: {
    ...mapState(useAuthStore, ["user", "authenticated"]),
  },
  methods: {
    ...mapActions(useAuthStore, ["getUser"]),
    async verify() {
      let key = this.$route.params.key;
      await this.$axios
        .get(`/verify/${key}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          if (this.authenticated) {
            this.getUser();
            this.$router.push({ name: "dashboard" });
          } else {
            this.$router.push({ name: "login" });
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
            this.$router.push({ name: "login" });
        });
    },
  },
  created() {
    this.verify();
  },
};
</script>