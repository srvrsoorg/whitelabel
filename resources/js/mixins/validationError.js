export default {
    methods: {
        // Method to hide error messages
        hideError() {
            $('small.error_message').hide()
        },

        // Method to display error messages
        displayError(error) {
            $.each(error.errors, function (key, value) {
                key = key.replaceAll(".", "_")
                $(`#${key}_message`).html(value[0]).fadeIn()
            })

        }
    }
}