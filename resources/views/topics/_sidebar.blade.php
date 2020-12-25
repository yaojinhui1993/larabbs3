<div class="card">
    <div class="card-body">
        <a href="{{ route('topics.create') }}" class="btn btn-success btn-block" aria-label="Left Align">
            <i class="fas fa-pencil-alt mr-2"></i>新建帖子
        </a>
    </div>
</div>

@if (count($activeUsers))
    <div class="card mt-4">
        <div class="text-center mt-1 mb-0 text-muted">活跃用户</div>
        <hr class="mt-2">

        @foreach($activeUsers as $activeUser)
            <a href="{{ route('users.show', $activeUser->id) }}" class="media mt-2">
                <div class="media-left media-middle mr-2 ml-1">
                    <img src="{{ $activeUser->avatar }}" class="media-object" width="24px" height="24px">
                </div>
                <div class="media-body">
                    <small class="media-heading text-secondary">{{ $activeUser->name }}</small>
                </div>
            </a>
        @endforeach
    </div>
@endif
