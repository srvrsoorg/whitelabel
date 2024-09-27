<button id="rzp-button1">Pay</button>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "{{ config('app.razorpay_key_id') }}", // Enter the Key ID generated from the Dashboard
    "amount": "5000", 
    "currency": "INR",
    "name": "Whitelable tech", //your business name
    "description": "Test Transaction",
    "order_id": "{{$orderId}}", 
    "callback_url": "https://eneqd3r9zrjok.x.pipedream.net/",
    "prefill": { 
        "name": "User",
        "email": "user@gmail.com",
        "contact": "9000090000"  
    },
    "notes": {
        "address": "Razorpay Corporate Office"
    },
    "theme": {
        "color": "#3399cc"
    }
};
var rzp1 = new Razorpay(options);
document.getElementById('rzp-button1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script>