@extends('layouts.app')

@section('title', 'Privacy Policy - سوقك')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Privacy Policy</h1>
                    <p class="mb-0 small">Last updated: July 16, 2025</p>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <p class="mb-0">This Privacy Notice for Mohammad ("we," "us," or "our"), describes how and why we might access, collect, store, use, and/or share ("process") your personal information when you use our services ("Services").</p>
                    </div>

                    <h2 class="h5 mt-4 text-primary">Summary of Key Points</h2>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item"><strong>What personal information do we collect?</strong> We do not collect personal or sensitive information from users.</li>
                        <li class="list-group-item"><strong>Do we collect data from third parties?</strong> No, we do not collect information from third parties.</li>
                        <li class="list-group-item"><strong>Do we send push notifications?</strong> Yes, you can opt out in your device settings.</li>
                        <li class="list-group-item"><strong>Do we collect data from minors?</strong> No, we do not knowingly collect or target children under 18.</li>
                    </ul>

                    <h2 class="h5 mt-4 text-primary">About Our Services</h2>
                    <p>Our application is a classified ads platform that allows users to browse and explore different categories such as:</p>
                    <ul>
                        <li>Real estate</li>
                        <li>Cars</li>
                        <li>Farms</li>
                        <li>Electronics</li>
                        <li>Schools</li>
                    </ul>
                    <p>Users can view item details, search, and apply filters to find what they're looking for.</p>

                    <h2 class="h5 mt-4 text-primary">How We Use Information</h2>
                    <p>To operate, improve, and maintain the app, and ensure a secure user experience.</p>

                    <h2 class="h5 mt-4 text-primary">Your Privacy Rights</h2>
                    <p>We do not collect any personal data, but if you have any concerns, feel free to contact us. You may also request to view or delete any information if applicable by law.</p>

                    <div class="card mt-4 border-primary">
                        <div class="card-header bg-light">
                            <h3 class="h6 mb-0">Contact Us</h3>
                        </div>
                        <div class="card-body">
                            <p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
                            <ul class="list-unstyled">
                                <li><strong>Name:</strong> Mohammad</li>
                                <li><strong>Email:</strong> <a href="mailto:soqakabb@gmail.com">soqakabb@gmail.com</a></li>
                                <li><strong>Country:</strong> United Arab Emirates</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer text-muted small">
                    <p class="mb-0">© {{ date('Y') }} سوقك. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    h1, h2, h3, h4, h5, h6 {
        font-weight: 600;
    }
    .text-primary {
        color: #4e73df !important;
    }
    .bg-primary {
        background-color: #4e73df !important;
    }
    .border-primary {
        border-color: #4e73df !important;
    }
</style>
@endsection