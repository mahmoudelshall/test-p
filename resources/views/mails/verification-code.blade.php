@if($data)
    <h3 style="text-align: center; color:rgb(10, 128, 128)">Hi {{ $data['name'] }}</h3>
    <h5> Your verification code is: </h5>
    <p style="font-weight:bold;text-align: center;color:blue"> {{ $data['code'] }} </p>
    <br>
    <p>Thanks<p>
@endif