<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Subscription Billing API",
    description: "REST API for Subscription Billing & Metering System"
)]

#[OA\Server(
    url: "http://localhost:8000",
    description: "Local Development Server"
)]

class SwaggerController extends Controller
{
    //
}