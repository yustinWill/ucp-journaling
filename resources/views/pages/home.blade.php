@extends('layouts.default')
@section('page_title')
    Home
@stop
@section('content')
    <div class="alert alert-primary alert-dismissible" role="alert">
        <h5 class="alert-heading mb-2"><b>Selamat Datang!</b></h5>
        <p class="mb-0">
            Akan bayak fitur menarik yang akan dikembangkan
        </p>
    </div>

    <div class="alert alert-primary alert-dismissible" role="alert">
        <h5 class="alert-heading mb-2"><b>PUSH NOTIF TEST!</b></h5>
        <p class="mb-0" id="notif">
            Listening for Messages...
          
        </p>
    </div>
@stop
@section('js_script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.Echo.channel("public-messages")
            .listen(".message.sent", (event) => {
                console.log("Received:", event);
                document.getElementById("notif").innerText = JSON.stringify(event, null, 2);
            });
    });
</script>
@stop
