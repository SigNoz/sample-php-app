<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
require_once 'utils.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use OpenTelemetry\API\Common\Log\LoggerHolder;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

LoggerHolder::set(new Logger('php-otlp-example', [new StreamHandler('php://stderr')]));

$otlpHttpEndpoint = setgetenv('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4318/v1/traces');
$otlpServiceName = setgetenv('OTEL_SERVICE_NAME', 'php-example-app');
$otlpInsecure = setgetenv('OTEL_EXPORTER_OTLP_INSECURE', 'true');

printf("Endpoint: %s\n", $otlpHttpEndpoint);
printf("Service Name: %s\n", $otlpServiceName);
printf("Insecure: %s\n", $otlpInsecure === 'true' ? 'true' : 'false');

$transport = (new OtlpHttpTransportFactory())->create($otlpHttpEndpoint, 'application/x-protobuf');
$exporter = new SpanExporter($transport);

echo 'Starting OTLP example';

$tracerProvider =  new TracerProvider(
    new SimpleSpanProcessor(
        $exporter
    )
);
$tracer = $tracerProvider->getTracer('io.signoz.examples.php');

$root = $span = $tracer->spanBuilder('root')->startSpan();
$scope = $span->activate();

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
$scope->detach();
echo PHP_EOL . 'OTLP example complete!  ';

echo PHP_EOL;
$tracerProvider->shutdown();
