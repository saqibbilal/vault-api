#!/bin/bash

# ==========================================
# Configuration - Adjust these for each project
# ==========================================
PROJECT_PREFIX="keepr"
REGION="us-east-1"
ACCOUNT_ID="157658493027"

# Derived Names
APP_REPO="${PROJECT_PREFIX}-app"
WEB_REPO="${PROJECT_PREFIX}-web"
ECR_BASE="${ACCOUNT_ID}.dkr.ecr.${REGION}.amazonaws.com"

echo "🚀 Starting Multi-Container Deployment for: ${PROJECT_PREFIX}"

# 1. Authenticate with AWS
echo "🔑 Authenticating with AWS ECR..."
aws ecr get-login-password --region ${REGION} | docker login --username AWS --password-stdin ${ECR_BASE}

# 2. Function to Ensure ECR Repo exists and Push Image
deploy_image() {
    local repo_name=$1
    local dockerfile=$2
    local image_uri="${ECR_BASE}/${repo_name}"

    echo "🔎 Checking ECR repository: ${repo_name}..."
    aws ecr describe-repositories --repository-names ${repo_name} --region ${REGION} > /dev/null 2>&1
    if [ $? -ne 0 ]; then
        echo "⚠️  Creating repository ${repo_name}..."
        aws ecr create-repository --repository-name ${repo_name} --region ${REGION}
    fi

    echo "📦 Building ${repo_name}..."
    # Using --platform linux/amd64 ensures compatibility with AWS Fargate
    docker build --platform linux/amd64 --no-cache -t ${repo_name} -f ${dockerfile} .

    echo "🏷️  Tagging and Pushing ${repo_name}..."
    docker tag ${repo_name}:latest ${image_uri}:latest
    if docker push ${image_uri}:latest; then
        echo "✅ Successfully pushed ${repo_name}"
    else
        echo "❌ ERROR: Push failed for ${repo_name}"
        exit 1
    fi
}

# 3. Execute Builds and Pushes
# Deploy the App (PHP-FPM) using standard Dockerfile
deploy_image "${APP_REPO}" "Dockerfile"

# Deploy the Web (Nginx) using the Nginx Dockerfile
deploy_image "${WEB_REPO}" "Dockerfile.nginx"

echo "=========================================="
echo "🎉 All images are live in ECR!"
echo "App Image: ${ECR_BASE}/${APP_REPO}:latest"
echo "Web Image: ${ECR_BASE}/${WEB_REPO}:latest"
echo "=========================================="
echo "Next step: Update your ECS Task Definition with these URIs."

CLUSTER="keepr-cluster"
SERVICE="keepr-service"

echo "🔄 Forcing new ECS deployment..."
aws ecs update-service \
  --cluster ${CLUSTER} \
  --service ${SERVICE} \
  --force-new-deployment
