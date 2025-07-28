@extends('layouts.app')

@section('title', 'Cảm ơn')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card">
                    <div class="card-body py-5">
                        <i class="bi bi-check-circle text-success" style="font-size: 5rem;"></i>
                        <h2 class="mt-4">Cảm ơn bạn!</h2>
                        <p class="lead">Chúng tôi đã nhận được phản hồi của bạn.</p>
                        <p>Ý kiến của bạn rất quan trọng và sẽ giúp chúng tôi cải thiện chất lượng.</p>
                        <a href="{{ route('khao-sat.index') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-house"></i> Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection