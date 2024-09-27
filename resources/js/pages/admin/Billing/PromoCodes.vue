<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="sm:flex justify-between items-center gap-5">
    <h1 class="text-xl text-[#31363f] whitespace-nowrap font-medium">
      Promo code
    </h1>
    <div class="xs:flex justify-end sm:mt-0 mt-3 items-center gap-5">
      <div class="relative w-full sm:min-w-[350px]">
        <input
          v-model="search"
          @input="handleSearch(current_page)"
          type="text"
          name="text"
          id="text"
          class="w-full block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          placeholder="Search"
        />
        <div
          class="text-white pointer-events-none absolute rounded-r-md inset-y-0 bg-[#F6F6F6] border border-primary right-0 items-center justify-center flex px-2"
        >
          <span class="material-symbols-outlined text-[22px] text-gray-400">
            search
          </span>
        </div>
      </div>
      <div class="flex justify-end gap-5 mt-5 xs:mt-0">
        <Button @click="openModal('create')" :class="['px-3.5']" type="button">
          Create
        </Button>
        <button
          @click="fetchPromoCodes(pagination.current_page)"
          type="button"
          class="bg-[#F6F6F6] items-center flex border-gray-200 border rounded-md text-gray-500 text-sm p-1.5 text-center"
        >
          <span
            :class="[
              refreshing ? 'fa-spin' : '',
              'material-symbols-outlined text-[22px] ',
            ]"
          >
            refresh
          </span>
        </button>
      </div>
    </div>
  </div>
  <div class="h-full mt-5">
    <Table :head="thead" v-if="promoCodeData && promoCodeData.length > 0">
      <tr
        class="border-b border-primary text-[#2c3138] text-[13px]"
        v-for="promoCode in promoCodeData"
        :key="promoCode.id"
      >
        <td class="py-4 px-4 pl-10">#{{ promoCode.id }}</td>
        <td class="whitespace-nowrap py-4 px-4">
          <span v-tooltip="promoCode.code">{{ promoCode.code }}</span>
        </td>
        <td class="py-4 px-4">
          {{ promoCode.usage_status ? promoCode.usage_status : "-" }}
        </td>
        <td class="py-4 px-4">{{ promoCode.discount }}%</td>
        <td class="py-4 px-4">
          {{ promoCode.expires_date ? promoCode.expires_date : "-" }}
        </td>

        <td class="whitespace-nowrap py-4 px-4">
          <span
            class="font-medium text-yellow-500 min-w-[150px]"
            v-if="promoCode.customer_type === 'all_customer'"
            >For All Customers</span
          >
          <span
            class="font-medium text-green-600 min-w-[150px]"
            v-else-if="promoCode.customer_type === 'new_customer'"
            >For New Customers</span
          >
        </td>
        <td class="whitespace-nowrap justify-center flex py-4 px-4">
          <div class="flex items-center gap-3">
            <button
              v-tooltip="'Edit'"
              class="p-1.5 bg-green-100 text-green-600 rounded-md flex items-center justify-center rounded-md,"
              @click="openModal('update', promoCode)"
            >
              <span class="material-symbols-outlined text-[20px]">
                edit_square
              </span>
            </button>
            <button
              v-tooltip="'Delete'"
              class="text-red-500 bg-red-100 p-1.5 items-center flex justify-center rounded-md"
              @click="openConfirmation(promoCode.id)"
            >
              <span class="material-symbols-outlined text-[20px]">
                delete
              </span>
            </button>
          </div>
        </td>
      </tr>
      <template #pagination>
        <div
          v-if="pagination.total > 10"
          :class="[
            pagination.total > 10 ? 'justify-between ' : 'justify-end',
            'sm:flex gap-3 py-5 px-4 ',
          ]"
        >
          <div v-if="pagination.total > 10">
            <Perpage v-model="per_page" :initialPerPage="pagination.per_page" @changePage="handlePerPageChange" />
          </div>
          <div v-if="promoCodeData.length > 0" class="mt-5 sm:mt-0">
            <Pagination
              :pagination="pagination"
              @page-change="handlePageChange"
            />
          </div>
        </div>
      </template>
    </Table>
    <template v-else>
      <TableSkeleton :heads="7" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="7" class="text-center text-sm px-6 py-5">
            {{ refreshing ? "Please Wait" : "No Promo Codes Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>
  <Modal
    :show="isOpenModal"
    @closeModal="closeModal"
    :modalTitle="modalTitle"
    :modelIcon="'shoppingmode'"
    :customClass="['md:max-w-4xl ']"
  >
    <div class="grid grid-cols-3 md:grid-cols-3 xl:grid-cols-3 gap-x-4">
      <div class="py-1 md:col-span-2 col-span-3">
        <label
          for="promo_code"
          class="text-tiny font-medium after:content-['*'] after:ml-0.5 after:text-red-500"
          >Promo Code
        </label>
        <input
          v-model="promoCode.code"
          type="code"
          name="code"
          id="code"
          class="w-full mt-2 block rounded-md border pl-3 border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0 focus:outline-none focus:box-shadow-none"
          placeholder="Promo Code"
        />
        <small
          class="text-red-500 text-xs error_message"
          id="code_message"
        ></small>
      </div>

      <div class="py-1 md:col-span-1 col-span-3">
        <label
          for="rate"
          class="text-tiny font-medium after:content-['*'] after:ml-0.5 after:text-red-500"
          >Customer Type
        </label>
        <select
          v-model="promoCode.customer_type"
          id="type"
          name="type"
          class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        >
          <option value="all_customer">For All Customers</option>
          <option value="new_customer">For New Customers Only</option>
        </select>
        <small
          class="text-red-500 text-xs error_message"
          id="customer_type_message"
        ></small>
      </div>
    </div>
    <div class="rounded-md px-4 py-2 mt-3 mb-2 text-[#308DEA] bg-[#EFF6FF]">
      <p class="text-sm">
        <b class="font-medium">Note:</b> You can set only one value: either
        "Usable" or "Expire At." If you select one, the other field will not be
        accepted.
      </p>
    </div>
    <div class="grid md:grid-cols-3 grid-cols-1 gap-x-4">
      <div class="py-1">
        <label
          for="discount"
          class="text-tiny font-medium after:content-['*'] after:ml-0.5 after:text-red-500"
          >Discount (%)
        </label>

        <input
          v-model="promoCode.discount"
          onwheel="this.blur()"
          type="number"
          name="discount"
          id="discount"
          class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          placeholder="0"
        />
        <small
          class="text-red-500 text-xs error_message"
          id="discount_message"
        ></small>
      </div>
      <div class="py-1">
        <label for="usable" class="text-tiny font-medium">Usable </label>
        <input
          v-model="promoCode.usable"
          onwheel="this.blur()"
          type="number"
          name="usable"
          id="usable"
          class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          placeholder="0"
        />
        <small
          class="text-red-500 text-xs error_message"
          id="usable_message"
        ></small>
      </div>
      <div class="py-1">
        <label for="expires at days" class="text-tiny font-medium"
          >Expires at
        </label>
        <input
          v-model="promoCode.expires_date"
          type="date"
          name="expires in dayes"
          id="expires_date"
          class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          placeholder="0"
        />
        <small
          class="text-red-500 text-xs error_message"
          id="expires_date_message"
        ></small>
      </div>
    </div>

    <div class="py-1 col-span-2">
      <label
        for="promo_code"
        class="text-tiny font-medium after:content-['*'] after:ml-0.5 after:text-red-500"
        >Description
      </label>
      <textarea
        rows="4"
        v-model="promoCode.description"
        type="text"
        name="Description"
        placeholder="Type here.."
        id="description"
        class="w-full mt-2 block rounded-md border pl-3 border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0 focus:outline-none focus:box-shadow-none"
      />
      <small
        class="text-red-500 text-xs error_message"
        id="description_message"
      ></small>
    </div>

    <div class="flex flex-row-reverse bg-white rounded-md mt-3">
      <div class="text-end rounded-md">
        <Button :disabled="processing" @click="SaveData">
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{
            processing
              ? "Please wait"
              : modalType === "create"
              ? "Save"
              : "Update"
          }}
        </Button>
      </div>
    </div>
  </Modal>

  <Confirmation
    :show="isOpenConfirmation"
    :showLoader="showLoader"
    :submitBtnTitle="`Yes, I'm sure`"
    :confirmationTitle="'Delete Promocode'"
    @confirm="deletePromoCode"
    @closeModal="closeModal"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[22px] text-red-500"
        >shoppingmode</span
      >
    </template>
    <template v-slot:content>
      <span class="text-gray-600 text-tiny"
        >Are you sure you want to delete this promo code?
      </span>
    </template>
  </Confirmation>
</template>
<script>
export default {
  name: "promocodes",
  data() {
    return {
      breadcrumb: {
        title: "Billing",
        icon: "lab_profile",
        pages: [
          {
            name: "Promo codes",
          },
        ],
      },
      isOpenConfirmation: false,
      showLoader: false,
      isOpenModal: false,
      pagination: null,
      processing: false,
      search: "",
      sort: "",
      filterSorting: [
        { title: "Usage (Descending)", value: "usage-desc" },
        { title: "Usage (Ascending)", value: "usage-asc" },
        { title: "Code (Descending)", value: "code-desc" },
        { title: "Code (Ascending)", value: "code-asc" },
        { title: "Discount (Descending)", value: "discount-desc" },
        { title: "Discount (Ascending)", value: "discount-asc" },
      ],
      thead: [
        {
          title: "ID",
          classes: "pl-10 ",
        },
        {
          title: "Code",
          classes: "min-w-[150px] max-w-[150px]",
        },
        {
          title: "Usage",
          classes: "",
        },

        {
          title: "Discount",
          classes: "",
        },
        {
          title: "Expired At",
          classes: "max-w-[150px] min-w-[150px]",
        },
        {
          title: "Customer Type",
          classes: "",
        },
        {
          title: "Actions",
          classes: "text-center",
        },
      ],
      modalTitle: null,
      modalType: null,
      promoCodeId: null,
      promoCodeData: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      per_page: 10,
      promoCode: {
        code: "",
        usable: "",
        description: "",
        discount: "",
        expires_in_days: "",
        expires_date: "",
      },
      current_page: 1,
      refreshing: false,
    };
  },
  created() {
    this.fetchPromoCodes();
    this.handleSearch = this.$debounce(this.fetchPromoCodes, 500);
  },
  methods: {
    openModal(type, data) {
      this.modalType = type;
      this.isOpenModal = true;
      if (type === "create") {
        this.modalTitle = "Create Promo Code";
        this.promoCode = {
          code: "",
          usable: "",
          description: "",
          discount: "",
          expires_in_days: "",
          expires_date: "",
          customer_type: "all_customer",
        };
      } else if (type === "update") {
        this.modalTitle = "Update Promo Code";
        this.promoCodeId = data.id;
        this.promoCode = {
          ...data,
          expires_date: data.expires_date,
          usable: data.usable === 0 ? "" : data.usable,
          customer_type: data.customer_type || "all_customer",
        };

        this.$nextTick(() => {
          if (this.$refs.promoCode) {
            this.$refs.promoCode.disabled = true;
          }
        });
      }
    },

    toggleNewCustomer() {
      this.promoCode.for_new_customers !== this.promoCode.for_new_customers;
      if (this.promoCode.for_new_customers) {
        this.promoCode.for_all_customers = false;
      }
    },
    toggleAllCustomer() {
      this.promoCode.for_all_customers !== this.promoCode.for_all_customers;
      if (this.promoCode.for_all_customers) {
        this.promoCode.for_new_customers = false;
      }
    },

    closeModal() {
      this.isOpenModal = false;
      this.isOpenConfirmation = false;
      this.showLoader = false;
      (this.promoCode = {
        code: "",
        usable: "",
        description: "",
        discount: "",
        expires_in_days: "",
        expires_date: "",
        for_new_customers: false,
        for_all_customers: false,
      }),
        (this.modalType = null);
      this.modalTitle = null;
      this.promoCodeId = null;
      this.processing = false;
    },
    openConfirmation(id) {
      this.isOpenConfirmation = true;
      this.promoCodeId = id;
    },

    async fetchPromoCodes(page = 1) {
      this.refreshing = true;
      let url = `/admin/promo-codes?page=${page}&per_page=${this.per_page}&search=${this.search}&sorting=${this.sort}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.promoCodeData = data.promoCodes.data;
          this.pagination = {
            current_page: data.promoCodes.current_page,
            last_page: data.promoCodes.last_page,
            per_page: data.promoCodes.per_page,
            total: data.promoCodes.total,
            next_page_url: data.promoCodes.next_page_url,
            prev_page_url: data.promoCodes.prev_page_url,
          };
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.refreshing = false;
        });
    },
    handlePageChange(newPage) {
      this.fetchPromoCodes(newPage);
    },
    handlePerPageChange() {
      this.fetchPromoCodes(1);
    },

    async SaveData() {
      this.hideError();
      this.processing = true;
      let url;
      try {
        if (this.modalType == "create") {
          url = await this.$axios.post("/admin/promo-codes", this.promoCode);
        } else if (this.modalType == "update") {
          url = await this.$axios.patch(
            `/admin/promo-codes/${this.promoCodeId}`,
            this.promoCode
          );
        }
        this.$toast.success(url.data.message);
        this.fetchPromoCodes();
        this.closeModal();
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.displayError(error.response.data);
        } else {
          this.$toast.error(error.response.data.message);
          this.closeModal();
        }
      } finally {
        this.processing = false;
      }
    },
    async deletePromoCode() {
      this.showLoader = true;
      await this.$axios
        .delete(`/admin/promo-codes/${this.promoCodeId}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchPromoCodes();
          this.closeModal();
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
          this.closeModal();
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
  },
};
</script>

<style>
input[type="date"]::-webkit-calendar-picker-indicator {
  font-size: 1rem;
  color: gray;
}
</style>