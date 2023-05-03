# Sample PHP App

Sample includes various types of examples:
1. [Simple Examples](#simple-examples)
    1. [Console Exporter](#console-exporter)
    2. [Send trace to collector (HTTP)](#send-trace-to-collector-http)
    3. [Send trace to collector (gRPC)](#send-trace-to-collector-grpc)
2. [Distributed Tracing](#distributed-tracing)

## Simple Examples

### Prerequisites

- Running instance of [SigNoz](https://signoz.io/docs/install/docker/)
- PHP 7.4+
- [Composer](https://getcomposer.org/download/) and installed dependencies
    ```bash
    # cd to the root of the project and run
    composer install
    ```
- For running gRPC example, gRPC PHP extension is required.
    ```bash
    # install gRPC PHP extension
    sudo pecl install grpc
    ```

### Console Exporter

```bash
php src/console-exporter.php
```

### Send trace to collector (HTTP)

In case of SigNoz not running on host machine, override the `OTEL_EXPORTER_OTLP_ENDPOINT` to the correct URL.
For example:

```bash
export OTEL_EXPORTER_OTLP_ENDPOINT=http://signoz.company.com:4318
```

To generate traces, run:

```bash
php src/collector-http.php
```

### Send trace to collector (gRPC)

In case of SigNoz not running on host machine, override the `OTEL_EXPORTER_OTLP_ENDPOINT` to the correct URL.
For example:

```bash
export OTEL_EXPORTER_OTLP_ENDPOINT=http://signoz.company.com:4317
```

To generate traces, run:

```bash
php src/collector-grpc.php
```

## Distributed Tracing

### Prerequisites

- Running instance of [SigNoz](https://signoz.io/docs/install/docker/)
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Running Distributed tracing example

```bash
# navigate to the /src/distributed-tracing
cd ./src/distributed-tracing

docker-compose run service-one composer install
docker-compose up

# in a separate terminal
$ curl localhost:8000/users/otel
```

#### Trace visualization on SigNoz

![Distributed Tracing visualization of SigNoz](./docs/distributed-tracing.png)

#### Application Metrics

![Application Metrics on SigNoz](./docs/application-metrics.png)
