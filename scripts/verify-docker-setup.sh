#!/bin/bash

# Script to verify Docker setup for Coolify deployment
# Run this before deploying to ensure everything is configured correctly

echo "🔍 Verifying Docker setup for Coolify deployment..."
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Dockerfile exists
echo "1. Checking Dockerfile..."
if [ -f "Dockerfile" ]; then
    echo -e "${GREEN}✓${NC} Dockerfile exists"
else
    echo -e "${RED}✗${NC} Dockerfile not found in root directory"
    exit 1
fi

# Check if .dockerignore exists
echo "2. Checking .dockerignore..."
if [ -f ".dockerignore" ]; then
    echo -e "${GREEN}✓${NC} .dockerignore exists"
else
    echo -e "${YELLOW}⚠${NC} .dockerignore not found (optional but recommended)"
fi

# Check if docker directory exists
echo "3. Checking docker configuration directory..."
if [ -d "docker" ]; then
    if [ -f "docker/nginx.conf" ] && [ -f "docker/supervisord.conf" ]; then
        echo -e "${GREEN}✓${NC} docker/ directory with required config files exists"
    else
        echo -e "${RED}✗${NC} docker/ directory exists but missing config files"
        exit 1
    fi
else
    echo -e "${RED}✗${NC} docker/ directory not found"
    exit 1
fi

# Check if files are in git
echo "4. Checking git tracking..."
if command -v git &> /dev/null; then
    if git ls-files | grep -q "Dockerfile"; then
        echo -e "${GREEN}✓${NC} Dockerfile is tracked in git"
    else
        echo -e "${YELLOW}⚠${NC} Dockerfile is NOT tracked in git (needs to be committed)"
        echo "   Run: git add Dockerfile .dockerignore docker/"
    fi
    
    if git ls-files | grep -q "\.dockerignore"; then
        echo -e "${GREEN}✓${NC} .dockerignore is tracked in git"
    else
        echo -e "${YELLOW}⚠${NC} .dockerignore is NOT tracked in git"
    fi
    
    if git ls-files | grep -q "docker/"; then
        echo -e "${GREEN}✓${NC} docker/ directory is tracked in git"
    else
        echo -e "${YELLOW}⚠${NC} docker/ directory is NOT tracked in git"
    fi
else
    echo -e "${YELLOW}⚠${NC} git not found, skipping git checks"
fi

# Check Dockerfile syntax (basic check)
echo "5. Checking Dockerfile syntax..."
if docker build --dry-run -f Dockerfile . &> /dev/null 2>&1; then
    echo -e "${GREEN}✓${NC} Dockerfile syntax appears valid"
else
    echo -e "${YELLOW}⚠${NC} Could not verify Dockerfile syntax (Docker might not be running)"
fi

# Check required files for build
echo "6. Checking required files for build..."
REQUIRED_FILES=("composer.json" "package.json" "vite.config.js" "artisan")
MISSING_FILES=()

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file exists"
    else
        echo -e "${RED}✗${NC} $file not found"
        MISSING_FILES+=("$file")
    fi
done

if [ ${#MISSING_FILES[@]} -gt 0 ]; then
    echo -e "${RED}✗${NC} Missing required files: ${MISSING_FILES[*]}"
    exit 1
fi

# Summary
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📋 Summary"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ ${#MISSING_FILES[@]} -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Ensure all files are committed: git add Dockerfile .dockerignore docker/"
    echo "2. Commit and push: git commit -m 'Add Dockerfile' && git push"
    echo "3. In Coolify, verify:"
    echo "   - Build Context: ."
    echo "   - Dockerfile Location: Dockerfile (or empty)"
    echo "   - Branch: correct branch with Dockerfile"
else
    echo -e "${RED}✗ Some checks failed${NC}"
    echo "Please fix the issues above before deploying to Coolify"
    exit 1
fi

