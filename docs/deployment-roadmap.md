**Keepr AWS Deployment Roadmap**
*Phase 1: ECR (deploy.sh)*
- Step 1: Build multi-container images (keepr-app & keepr-web) using deploy.sh.
- Step 2: Tag images with AWS Account ID and Region.
- Step 3: Push images to ECR repositories.
*Phase 2: RDS (Database Layer)*
- Step 4: Create a Subnet Group (ensuring the DB is in the right "zone").
- Step 5: Provision a PostgreSQL instance (Free Tier db.t3.micro or db.t4g.micro).
- Step 6: Configure Security Groups to allow the Backend to talk to the Database.
- Step 7: Run migrations (we'll do this via a temporary ECS task).
*Phase 3: ECS (Compute Layer)*
- Step 8: Create an ECS Cluster (The logical grouping for your services).
- Step 9: Create a Task Definition (This is the "Blueprint" where we tell AWS to run the App and Web containers together).
- Step 10: Configure an Application Load Balancer (ALB) to route public traffic to your Nginx container.
- Step 11: Create the ECS Service to launch and maintain your containers.
*Phase 4: Frontend & Connectivity*
- Step 12: Deploy Frontend to Vercel.
- Step 13: Point NEXT_PUBLIC_API_URL to the AWS Load Balancer DNS.
