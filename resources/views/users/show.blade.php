@extends('layouts.app')

@section('title', $user->name . ' 的个人中心')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-3 hidden-sm hidden-xs user-info">
            <div class="card">
                <img src="https://cdn.learnku.com/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/600/h/600" alt="{{ $user->name }}" class="card-img-top">
                <div class="card-body">
                    <h5><strong>个人简介</strong></h5>
                    <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Facere et velit expedita ab reprehenderit? In optio dicta nemo! Id voluptatem numquam odio hic ullam error aspernatur repellat, ab quod dolore ut in pariatur nihil quisquam reiciendis exercitationem blanditiis quidem iure nisi facilis nulla fugit perferendis aut earum. Eum, quod aliquid.</p>
                    <hr>
                    <h5><strong>注册于</strong></h5>
                    <p>January 01 1901</p>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="mb-0" style="font-size: 22px;">
                        <small>{{ $user->email }}</small>
                    </h1>
                </div>
            </div>

            <hr>

            {{-- 用户发布的内容 --}}
            <div class="card">
                <div class="card-body">
                    暂无数据~
                </div>
            </div>

        </div>



    </div>
@endsection
