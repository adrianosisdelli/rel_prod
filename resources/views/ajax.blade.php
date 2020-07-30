<html>
<head>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body>
    <button id="btnclicar">Get</button>
    <p id="p-result">Texto aqui</p>

    <script>
    $(document).ready(function(){
        
        $("#btnclicar").click(function(){
            $.ajax({
                url: 'https://cat-fact.herokuapp.com/api/facts/1',
                beforeSend: function(){
                    alert('Antes de enviar');
                },
                error: function(error){
                    alert(JSON.stringify(error));
                },
                success: function(){

                }
            });
        });
           
    });
    </script>
</body>
</html>