# Docker Deployment Documentation

This directory contains documentation for deploying and managing the Laravel application with Docker and Coolify.

## Documentation Files

### 📘 [DOCKER_MARIADB_SETUP.md](./DOCKER_MARIADB_SETUP.md)
Complete manual for setting up and connecting to a MariaDB Docker container. Includes:
- Setting up MariaDB in Coolify
- Docker network configuration
- Laravel database configuration
- Connection methods
- Troubleshooting guide
- Best practices

### ⚡ [MARIADB_QUICK_REFERENCE.md](./MARIADB_QUICK_REFERENCE.md)
Quick reference guide with common commands for:
- Database operations
- User management
- Backup & restore
- Troubleshooting
- Monitoring

## Quick Links

- **Dockerfile**: Located at `/Dockerfile` in project root
- **Docker Compose Example**: `/docker-compose.example.yml`
- **Docker Config**: `/docker/` directory (nginx.conf, supervisord.conf)

## Getting Started

1. **For Coolify Deployment:**
   - Read [DOCKER_MARIADB_SETUP.md](./DOCKER_MARIADB_SETUP.md) for database setup
   - Ensure Dockerfile is committed to your repository
   - Configure environment variables in Coolify

2. **For Local Development:**
   - Use `docker-compose.example.yml` as a template
   - Copy to `docker-compose.yml` and configure
   - See quick reference for common commands

## Support

For issues or questions:
1. Check the troubleshooting sections in the documentation
2. Review Coolify logs
3. Check Docker container logs

