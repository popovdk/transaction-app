group "default" {
    targets = ["app", "nginx"]
}

variable "CI_REGISTRY_IMAGE" {
    default = "github.com/popovdk/transaction-app"
}
variable "CI_COMMIT_SHORT_SHA" {
    default = "latest"
}
variable "CI_COMMIT_REF_NAME" {
    default = "transaction-app"
}
variable "CI_OUTPUT_TYPE" {
    default = "image"
}
variable "CI_DEFAULT_BAKE_TAG" {
    default = "${CI_COMMIT_SHORT_SHA}"
}

target "app-builder" {
    dockerfile = "./docker/app-builder/Dockerfile"
    tags = [
        "app-builder:latest"
    ]
    output = ["type=cacheonly"]
    platforms = ["linux/amd64"]
}

target "app" {
    contexts = {
        app_builder = "target:app-builder"
    }
    dockerfile = "./docker/app/Dockerfile"
    tags = [
        "${CI_REGISTRY_IMAGE}/${CI_COMMIT_REF_NAME}/app:${CI_COMMIT_SHORT_SHA}",
        "${CI_REGISTRY_IMAGE}/${CI_COMMIT_REF_NAME}/app:latest",
        "${CI_REGISTRY_IMAGE}/app:${CI_DEFAULT_BAKE_TAG}"
    ]
    output = [
        "type=${CI_OUTPUT_TYPE}"
    ]
    platforms = ["linux/amd64"]
}

target "nginx" {
    contexts = {
        app_builder = "target:app-builder"
    }
    dockerfile = "./docker/nginx/Dockerfile"
    tags = [
        "${CI_REGISTRY_IMAGE}/${CI_COMMIT_REF_NAME}/nginx:${CI_COMMIT_SHORT_SHA}",
        "${CI_REGISTRY_IMAGE}/${CI_COMMIT_REF_NAME}/nginx:latest",
        "${CI_REGISTRY_IMAGE}/nginx:${CI_DEFAULT_BAKE_TAG}"
    ]
    output = [
        "type=${CI_OUTPUT_TYPE}"
    ]
    platforms = ["linux/amd64"]
}
