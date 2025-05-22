#!/bin/bash

# Nettoyer les conteneurs et déploiements existants
echo "Cleaning up existing containers and deployments..."
docker compose down --remove-orphans
docker rm -f $(docker ps -aq) 2>/dev/null || true
kubectl delete deployment --all -n arises
kubectl delete service --all -n arises

# Build des images
echo "Building Docker images..."
echo "Building frontend image..."
docker build -t arises-frontend:latest ./frontend
if [ $? -ne 0 ]; then
    echo "Error building frontend image"
    exit 1
fi

echo "Building backend image..."
docker build -t arises-backend:latest ./backend
if [ $? -ne 0 ]; then
    echo "Error building backend image"
    exit 1
fi

# Vérifier que les images sont bien créées
echo "Verifying images..."
docker images | grep arises

# Appliquer les configurations Kubernetes
echo "Applying Kubernetes configurations..."
kubectl apply -f k8s/deployment.yaml -f k8s/service.yaml

# Vérifier le statut du déploiement
echo "Checking deployment status..."
kubectl get pods -n arises
kubectl get services -n arises 