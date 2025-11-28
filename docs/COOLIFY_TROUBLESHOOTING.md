# Coolify Deployment Troubleshooting Guide

## Error: "No application encryption key has been specified"

**Quick Fix:**

1. Generate an APP_KEY:
   ```bash
   php artisan key:generate --show
   ```

2. In Coolify → Your Application → Environment Variables:
   - Add `APP_KEY` with the generated value (starts with `base64:`)
   - Save and redeploy

See the [APP_KEY section](#setting-app_key-in-coolify) below for detailed instructions.

---

## Error: "failed to read dockerfile: open Dockerfile: no such file or directory"

This error occurs when Coolify cannot find the Dockerfile in the build context. Follow these steps to resolve:

### Step 1: Verify Files Are Committed to Git

The Dockerfile and related files must be committed to your git repository:

```bash
# Check if files are tracked
git ls-files | grep -E "(Dockerfile|\.dockerignore|docker/)"

# If files are not tracked, add and commit them:
git add Dockerfile .dockerignore docker/
git commit -m "Add Dockerfile and Docker configuration for Coolify"
git push
```

### Step 2: Verify Coolify Build Context Settings

In Coolify, check your application settings:

1. **Go to your application in Coolify**
2. **Navigate to Settings → Build**
3. **Verify the following:**

   - **Build Context**: Should be `.` (dot = root directory)
   - **Dockerfile Location**: Should be `Dockerfile` or `.` (if in root)
   - **Dockerfile Path**: Leave empty if Dockerfile is in root, or specify path like `./Dockerfile`

### Step 3: Check Branch/Commit

Ensure you're deploying from the correct branch that contains the Dockerfile:

1. **In Coolify → Your Application → Settings**
2. **Check "Source" settings:**
   - Branch: Should be the branch with Dockerfile (usually `main` or `master`)
   - Commit: Should be the latest commit with Dockerfile

### Step 4: Verify Repository Structure

Your repository structure should look like this:

```
your-repo/
├── Dockerfile          ← Must be in root
├── .dockerignore       ← Should be in root
├── docker/
│   ├── nginx.conf
│   └── supervisord.conf
├── app/
├── config/
├── public/
└── ... (other Laravel files)
```

### Step 5: Manual Verification

Test if Docker can find the file locally:

```bash
# From your project root
docker build -t test-build .
```

If this works locally, the issue is with Coolify's configuration.

## Common Coolify Configuration Issues

### Issue: Build Context is Wrong

**Symptom:** Dockerfile not found error

**Solution:**
- Set **Build Context** to `.` (root directory)
- Set **Dockerfile Location** to `Dockerfile` or leave empty

### Issue: Dockerfile in Subdirectory

If your Dockerfile is in a subdirectory (not recommended):

**Solution:**
- Set **Build Context** to the subdirectory (e.g., `./docker`)
- Set **Dockerfile Location** to `Dockerfile` or the relative path

### Issue: Wrong Branch

**Symptom:** Files exist locally but not in deployed branch

**Solution:**
1. Verify branch in Coolify matches your local branch
2. Push Dockerfile to the correct branch:
   ```bash
   git checkout main  # or your deployment branch
   git add Dockerfile .dockerignore docker/
   git commit -m "Add Dockerfile"
   git push origin main
   ```

## Quick Fix Checklist

- [ ] Dockerfile exists in repository root
- [ ] Dockerfile is committed to git
- [ ] Dockerfile is pushed to the branch Coolify is using
- [ ] Build Context in Coolify is set to `.`
- [ ] Dockerfile Location in Coolify is `Dockerfile` or empty
- [ ] Correct branch is selected in Coolify
- [ ] Repository is properly connected in Coolify

## Testing Locally

Before deploying to Coolify, test the Dockerfile locally:

```bash
# Build the image
docker build -t laravel-app .

# If build succeeds, the Dockerfile is correct
# If it fails, fix the errors first
```

## Coolify-Specific Settings

### Recommended Coolify Configuration

**Build Settings:**
- **Build Pack**: Docker
- **Build Context**: `.`
- **Dockerfile Location**: `Dockerfile` (or leave empty)
- **Build Command**: (leave empty, Dockerfile handles it)

**Environment Variables:**
Make sure these are set in Coolify:
```env
APP_KEY=base64:your-generated-key-here  # REQUIRED - See below for how to generate
DB_CONNECTION=mariadb
DB_HOST=mariadb  # or your database service name
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=your_password
```

### Setting APP_KEY in Coolify

**Error:** `No application encryption key has been specified`

**Solution:**

1. **Generate an APP_KEY:**
   ```bash
   # On your local machine or in a temporary container
   php artisan key:generate --show
   ```
   This will output something like: `base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

2. **Set in Coolify:**
   - Go to your application in Coolify
   - Navigate to **Environment Variables**
   - Add a new variable:
     - **Key:** `APP_KEY`
     - **Value:** `base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` (the generated key)
   - Save and redeploy

3. **Important Notes:**
   - The APP_KEY must start with `base64:`
   - Never change the APP_KEY after data has been encrypted (sessions, cookies, etc.)
   - Keep the APP_KEY secure and consistent across deployments
   - If you lose the APP_KEY, all encrypted data will be lost

## Still Having Issues?

1. **Check Coolify Logs:**
   - Go to your application in Coolify
   - Click on "Show Debug Logs" during deployment
   - Look for specific error messages

2. **Verify Git Repository:**
   ```bash
   # Check if Dockerfile is in the repository
   git show HEAD:Dockerfile
   ```

3. **Check Build Context:**
   - In Coolify, the build happens in a temporary directory
   - Ensure your Dockerfile path is relative to the build context

4. **Contact Support:**
   - Check Coolify documentation: https://coolify.io/docs
   - Review Coolify GitHub issues

## Additional Resources

- [Coolify Documentation](https://coolify.io/docs)
- [Docker Build Context](https://docs.docker.com/build/building/context/)
- [Dockerfile Best Practices](https://docs.docker.com/develop/develop-images/dockerfile_best-practices/)

