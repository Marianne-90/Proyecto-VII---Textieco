{{-- resources/views/admin/notifications/index.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Notificaciones</h1>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('admin.notifications.readAll') }}">
            @csrf
            <button class="btn btn-secondary">Marcar todas como vistas</button>
        </form>
        <form method="POST" action="{{ route('admin.notifications.destroyAll') }}">
            @csrf @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('¿Eliminar todas?')">Eliminar todas</button>
        </form>
    </div>
</div>

<div class="mb-3 text-sm">
    <strong>No leídas:</strong> {{ $unreadCount }}
</div>

<ul class="divide-y">
@foreach ($notifications as $n)
    @php $data = $n->data ?? []; @endphp
    <li class="py-3 flex items-start justify-between {{ is_null($n->read_at) ? 'bg-yellow-50' : '' }}">
        <div class="pr-4">
            <div class="font-medium">{{ $data['title'] ?? class_basename($n->type) }}</div>
            <div class="text-sm text-gray-700">{{ $data['message'] ?? '' }}</div>
            @if (!empty($data['url']))
                <a href="{{ $data['url'] }}" class="text-sm text-blue-600 underline">Ver detalle</a>
            @endif
            <div class="text-xs text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        <div class="flex items-center gap-2">
            @if (is_null($n->read_at))
            <form method="POST" action="{{ route('admin.notifications.read', $n->id) }}">
                @csrf
                <button class="btn btn-light">Marcar vista</button>
            </form>
            @endif
            <form method="POST" action="{{ route('admin.notifications.destroy', $n->id) }}">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Eliminar</button>
            </form>
        </div>
    </li>
@endforeach
</ul>

<div class="mt-4">
    {{ $notifications->links() }}
</div>
@endsection
