runtime_files	:= src/bootstrap \
					src/LambdaRuntimePhp

binaries		:= src/bin/*

all: build/runtime.zip

$(binaries): Dockerfile
	docker build -t lambda-php-runtime-binary-builder .
	docker run --entrypoint bash --name lambda-php-runtime-binary-builder lambda-php-runtime-binary-builder
	docker cp lambda-php-runtime-binary-builder:/tmp/php-7-bin/bin src
	docker rm lambda-php-runtime-binary-builder
	touch -m src/bin/*

build/runtime.zip: $(binaries) $(runtime_files)
	mkdir -p build
	cd src && zip -r ../build/runtime.zip bin LambdaRuntimePhp bootstrap

.PHONY : clean
clean:
	-rm $(binaries) build/runtime.zip
