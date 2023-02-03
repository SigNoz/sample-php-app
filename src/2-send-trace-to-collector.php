<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

// Author: Pranshu Chittora x SigNoz.io
// Create Trace (Manual Instrumentation) with OTel-PHP
// This sample demonstrates manual instrumentation with OpenTelemetry.
// Traces being sent to SigNoz OTel Collector.
// References - https://github.com/open-telemetry/opentelemetry-php


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use OpenTelemetry\Contrib\OtlpHttp\Exporter as OTLPExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\SDK\Trace\TracerProviderFactory;

putenv('OTEL_EXPORTER_OTLP_ENDPOINT=http://localhost:4318/v1/traces');
putenv('OTEL_SERVICE_NAME=signoz-php-app');

$tracerProvider = (new TracerProviderFactory())->create();
$tracer = $tracerProvider->getTracer('io.signoz.php.example');

$root = $span = $tracer->spanBuilder('root')->setSpanKind(SpanKind::KIND_SERVER)->startSpan();
$rootScope = $span->activate();

for ($i = 0; $i < 3; $i++) {
    // start a span, register some events
    $span = $tracer->spanBuilder('loop-' . $i)->startSpan();

    $span->setAttribute('remote_ip', '1.2.3.4')
        ->setAttribute('country', 'USA');

    $span->addEvent('found_login' . $i, [
        'id' => $i,
        'username' => 'otuser' . $i,
    ]);
    $span->addEvent('generated_session', [
        'id' => md5((string) microtime(true)),
    ]);

    $span->end();
}
$root->end();
$rootScope->detach();
echo PHP_EOL . 'OTLPExample complete!  ';

echo PHP_EOL;
