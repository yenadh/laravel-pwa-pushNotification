<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PWA-Push Notification</title>
    <meta name="theme-color" content="#6777ef" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="https://cc52-124-43-68-34.ngrok-free.app/manifest.json">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const baseUrl = "https://cc52-124-43-68-34.ngrok-free.app/"

        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("/sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                },
            );
        } else {
            console.error("Service workers are not supported.");
        }

        function saveSub(sub) {
            $.ajax({
                type: 'post',
                url: `${baseUrl}save-sub`,
                data: {
                    '_token': "{{ csrf_token() }}",
                    'sub': sub
                },
                success: function(data) {
                    console.log(data);
                }
            })
        }

        function sendNotification() {
            $.ajax({
                type: 'POST',
                url: `${baseUrl}send-notification`,
                data: {
                    '_token': "{{ csrf_token() }}",
                    'title': $('#title').val(),
                    'message': $('#content').val(),
                    'url': $('#url').val(),
                },
                success: function(data) {
                    console.log(data);
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }


        function askForPermission() {
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    navigator.serviceWorker.ready.then((sw) => {
                        sw.pushManager.subscribe({
                            userVisibleOnly: true,
                            // applicationServerKey: "BJTRPset8ahu7zcHQ2jBBaLgJxsDp_xHiyngUy9wacd36v-xOeanlY7sFvv6uP0aJBX0ZRseaBNLNRbqravdPkI"
                            applicationServerKey: "BNSR0cVgFTexMi-vsaPqYIYW2wIDzeFmwYF9PYSD1G8T6d9G6ZhoxzseDRU2RPg81Jbf7CtQTzPypLyVcUdbO68"
                        }).then((subscription) => {
                            console.log(subscription)
                            saveSub(JSON.stringify(subscription));
                        })
                    })
                }
            })
        }
    </script>
</head>

<body>
    <div>
        <label for="title">Title</label>
        <input type="text" id="title" name="title">

        <label for="content">Content</label>
        <input type="text" id="content" name="content">

        <label for="url">URL</label>
        <input type="text" id="url" name="url">

        <button onclick="askForPermission()">Subscribe</button>
        <button onclick="sendNotification()">Send</button>
    </div>

</body>

</html>
