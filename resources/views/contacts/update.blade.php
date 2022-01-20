@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-md-6 offset-3">
            <div class="row justify-content-center">
                <div class="card">
                    <div class="card-header">{{ __('Contact Update') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        {!! Form::model($contact, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['contact.update', $contact->id],'id'=>'editContact']) !!}
                        <input name="id" id="id" type="hidden" value="{{ $contact->id }}">
                        @csrf

                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="name">Name:</label>
                                {!! Form::text('name', null, array('id' => 'name', 'class'=>'form-control','autofocus','maxlength'=>150)) !!}
                                <span id="name-error" class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="email">Email:</label>
                                {!! Form::text('email', null, array('id' => 'email', 'class'=>'form-control','autofocus','maxlength'=>150)) !!}
                                <span id="email-error" class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="phone">Phone:</label>
                                {!! Form::text('phone', null, array('id' => 'phone', 'class'=>'form-control','autofocus','maxlength'=>100)) !!}
                                <span id="phone-error" class="text-danger">{{ $errors->first('phone') }}</span>
                            </div>
                        </div>
                        {!! Form::submit('Update', ['id' => 'submit', 'class' => 'btn btn-primary text-uppercase pull-right', 'name' => 'Save']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-javascript')
    <script>
        $(document).ready(function () {
            $(document).ready(function () {
                jQuery.validator.addMethod("lettersOnly", function (value, element) {
                    return this.optional(element) || /^[a-zA-Z\s]*$/i.test(value);
                }, "{{ __('Enter Name Must Be Character') }}");
            });

            $('#editContact').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 1,
                        maxlength: 150,
                        lettersOnly: true
                    },
                    email: {
                        required: true,
                        email: true,
                        minlength: 1,
                        maxlength: 150
                    },
                    phone: {
                        required:true,
                        number: true,
                        minlength: 10,
                        maxlength: 12
                    }
                },
                messages: {
                    name: {
                        required: "{{ __('Enter Mame') }}",
                        minlength: "{{ __('Enter Name min 1') }}",
                        maxlength: "{{ __('Enter Name max 150') }}"
                    },
                    email: {
                        required: "{{ __('Enter Email') }}",
                        email: "Email must be a valid email address",
                        minlength: "{{ __('Enter Email min 1') }}",
                        maxlength: "{{ __('Enter Email max 150') }}"
                    },
                    phone:{
                        required: "{{ __('Enter Phone') }}",
                        number: "{{ __('Enter number only') }}",
                        minlength: "{{ __('Enter Phone min 10') }}",
                        maxlength: "{{ __('Enter Phone max 10') }}"
                    }
                },
                submitHandler: function(form) {
                    var name = $('#name').val();
                    var email = $('#email').val();
                    var phone = $('#phone').val();

                    formId = 'editContact';
                    id = $('#id').val();
                    formAction = $('#editContact').attr('action');
                    formMethod = 'patch';

                    if(formAction != '' && formMethod != '') {

                        $.ajax({
                            url: formAction,
                            type: formMethod,
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                'id': id,
                                'name': name,
                                'email': email,
                                'phone': phone
                            },
                            success: function (response) {
                                if (response.status == "success") {
                                    window.location.href = '{{ route("contact.index") }}';
                                }
                            },
                            error: function (xhr, status, error) {
                                responseText = jQuery.parseJSON(xhr.responseText);
                                jQuery.each(responseText.errors, function (key, value) {
//                                    $("#" + formId + " #" + key + "-error").parent().parent().addClass('is-invalid');
                                    $("#" + formId + " #" + key + "-error").html(value);
                                });
                            }
                        })
                    }
                }
            });
        });
    </script>
@endsection