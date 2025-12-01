@extends('emails.layouts.staynest')

@section('title', 'Nueva consulta')

@section('content')
<h2 style="margin: 0 0 20px 0; color: #2c5aa0; font-size: 20px;">Nueva consulta</h2>

<table style="width: 100%; margin: 20px 0; border-collapse: collapse; background: #f8f9fa; border-radius: 2px; overflow: hidden;">
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; width: 30%;"><strong>Nombre:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;">{{ $data['name'] }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Email:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;">{{ $data['email'] }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; vertical-align: top;"><strong>Mensaje:</strong></td>
        <td style="padding: 12px; white-space: pre-wrap;">{{ $data['message'] }}</td>
    </tr>
</table>
@endsection