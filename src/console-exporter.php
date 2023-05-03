<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

// Author: SigNoz.io
// Getting Started with OTel-PHP
// This sample demonstrates basic application instrumentation with OpenTelemetry.
// Traces not not being sent to any collector/service, though they are outputted in the console.
// References - https://github.com/open-telemetry/opentelemetry-php

use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporterFactory;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\API\Trace\SpanKind;

echo 'Starting ConsoleSpanExporter' . PHP_EOL;

$tracerProvider =  new TracerProvider(
    new SimpleSpanProcessor(
        (new ConsoleSpanExporterFactory())->create()
    )
);

$tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php',);

$rootSpan = $tracer->spanBuilder('root')->setSpanKind(SpanKind::KIND_SERVER)->startSpan();
$rootScope = $rootSpan->activate();

try {
    $span1 = $tracer->spanBuilder('foo')->setSpanKind(SpanKind::KIND_SERVER)->startSpan();

    print_r($span1);
    $scope1 = $span1->activate();
    try {
        $span2 = $tracer->spanBuilder('bar')->setSpanKind(SpanKind::KIND_SERVER)->startSpan();
        echo 'OpenTelemetry welcomes PHP' . PHP_EOL;
    } finally {
        $span2->end();
    }
} finally {
    $span1->end();
    $scope1->detach();
}
$rootSpan->end();
$rootScope->detach();
