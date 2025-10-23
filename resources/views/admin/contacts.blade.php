@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                {{-- Título: Todos los Mensajes --}}
                <h3>Todos los Mensajes</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            {{-- Dashboard --}}
                            <div class="text-tiny">Panel de Control</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        {{-- Todos los Mensajes --}}
                        <div class="text-tiny">Todos los Mensajes</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                {{-- Placeholder: Buscar aquí... --}}
                                <input type="text" placeholder="Buscar aquí..." class="" name="name" tabindex="2" value=""
                                    aria-required="true" required="">
                            </fieldset>
                            <div class="button-submit">
                                <button class="" type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        @if(Session::has('status'))
                            {{-- Mensaje de éxito --}}
                            <p class="alert alert-success text-center">{{ Session::get('status') }}</p>
                        @endif
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Comentario</th>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $contact)
                                    <tr>
                                        <td>{{ $contact->id }}</td>
                                        <td>{{ $contact->name }}</td>
                                        <td>{{ $contact->phone }}</td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ $contact->comment }}</td>
                                        <td>{{ $contact->created_at }}</td>
                                        <td>
                                            <div class="list-icon-function">
                                                <form action="{{ route('admin.contacts.delete', ['id' => $contact->id]) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="item text-danger delete">
                                                        <i class="icon-trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{-- Paginación, usa la plantilla de Bootstrap 5 --}}
                    {{ $contacts->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('.delete').on("click", function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                // Traducción del cuadro de diálogo SweetAlert
                swal({
                    title: "¿Estás seguro?",
                    text: "¿Quieres eliminar este mensaje?", // Cambié "coupon" por "mensaje"
                    type: "warning",
                    buttons: ["NO", "SÍ"],
                    confirmButtonColor: '#dc3545'
                }).then(function (result) {
                    if (result) {
                        // console.log('➡️ Ruta a la que se enviará el formulario:', form.attr('action'));
                        // console.log('➡️ Método HTML:', form.attr('method'));

                        form.submit();
                    }
                });
            });
        });

    </script>
@endpush
