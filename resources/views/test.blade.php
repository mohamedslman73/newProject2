<body>

<h1>Start</h1>

<input type="text" id="start_id">
<button id="start">Start</button>
<hr>

<h1>Chat</h1>

<input type="text" id="sendMessagedd">
<button id="sendMessage">send</button>

</body>
<script src="{{asset('js/socket.io.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    socket = io("http://127.0.0.1:3000");
    socket.on('connect',function() {
        toastr.success('{{__("Successfully connect to chat server")}}', '{{__('Success')}}', {"closeButton": true});
    });


    socket.on('message',function(message) {
        console.log(message);
    });

    socket.on('status',function(message) {
        alert(message);
    });




    $('#start').click(function(){
        socket.emit('start',$('#start_id').val());
    });

    $('#sendMessage').click(function(){
        socket.emit('sendMessage',$('#sendMessagedd').val());
    });





</script>
