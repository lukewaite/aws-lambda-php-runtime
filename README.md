# aws-lambda-php-runtime
> This project is a WIP php runtime environment

# Why another?
The Stackery php runtime is tailored to API Gateway, while I wanted a more "traditional"
Lambda runtime. ie, load a file, execute a function.

Further, I wanted a runtime which provided minimal pollution of the global php environment
when executing. We define no global functions, and we depend on no composer packages.
This reduces the runtime's chance of conflicting with a given function's dependencies or
application code.

# How to build
The binaries must be compiled on Amazon Linux 2017.03 - which is the environment Lambda
runs in. We use docker for this purpose, so you'll need this installed.

Run `make all`, and you'll end up with a `build/runtime.zip`. You can upload this zip file
as a custom lambda layer, and then select that you are providing a runtime when you create
your lambda function.

## Credits 

#### https://github.com/stackery/php-lambda-layer
The Stackery runtime project provided a great starting point, and sample code for
interacting with the AWS Lambda runtime APIs.

#### https://aws.amazon.com/blogs/apn/aws-lambda-custom-runtime-for-php-a-practical-example
A great writeup showing how to get started writing your own runtime, with examples in PHP!