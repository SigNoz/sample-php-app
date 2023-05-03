<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
require_once 'utils.php';

use OpenTelemetry\API\Common\Signal\Signals;
use OpenTelemetry\Contrib\Grpc\GrpcTransportFactory;
use OpenTelemetry\Contrib\Otlp\OtlpUtil;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

\OpenTelemetry\API\Common\Log\LoggerHolder::set(new \Monolog\Logger('grpc', [new \Monolog\Handler\StreamHandler('php://stderr')]));

$otlpHttpEndpoint = setgetenv('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4317');
$otlpServiceName = setgetenv('OTEL_SERVICE_NAME', 'php-example-app');
$otlpInsecure = setgetenv('OTEL_EXPORTER_OTLP_TRACES_PROTOCOL', 'grpc');
$otlpInsecure = setgetenv('OTEL_EXPORTER_OTLP_INSECURE', 'true');

printf("Endpoint: %s\n", $otlpHttpEndpoint);
printf("Service Name: %s\n", $otlpServiceName);
printf("Insecure: %s\n", $otlpInsecure === 'true' ? 'true' : 'false');

$transport = (new GrpcTransportFactory())->create($otlpHttpEndpoint . OtlpUtil::method(Signals::TRACE));
$exporter = new SpanExporter($transport);
echo 'Starting OTLP GRPC example';

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
echo PHP_EOL . 'OTLP GRPC example complete!  ';

echo PHP_EOL;
$tracerProvider->shutdown();