{{-- Bootstrap Notifications using Prologue Alerts --}}
<script type="text/javascript" defer>
    document.addEventListener('DOMContentLoaded', (event) => {

        /*PNotify.prototype.options.styling = "bootstrap4";
        PNotify.prototype.options.styling = "fontawesome";*/

        @foreach (Alert::getMessages() as $type => $messages)
        @foreach ($messages as $message)

        $(function () {
            /*new PNotify({
              // title: 'Regular Notice',
              text: "{!! str_replace('"', "'", $message) !!}",
                type: "{{ $type }}",
                icon: false
              });*/

            console.log('{!! str_replace('"', "'", $message) !!}');

            toastr["{{ $type }}"]("{!! str_replace('"', "'", $message) !!}");
        });

        @endforeach
        @endforeach
    });
</script>
