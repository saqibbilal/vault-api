🏗️ Phase 3: ECS (Compute Layer or The Orchestration)
We are going to set this up using Fargate, which is the "Serverless" way to run containers. You don't have to manage servers; you just tell AWS "run this task," and it handles the rest.
Step 1: Create the ECS Cluster
1.	Go to Elastic Container Service (ECS) > Clusters.
2.	Click Create cluster.
3.	Cluster name: keepr-cluster.
4.	Infrastructure: Ensure AWS Fargate (serverless) is selected.
5.	Click Create. (This only takes a few seconds).
________________________________________
Step 2: Create the Task Definition (The "Blueprint")
This is the most critical part. This is where we define the Sidecar Pattern (Nginx + PHP-FPM).
1.	In the ECS sidebar, go to Task Definitions > Create new task definition.
2.	Task definition family: keepr-task.
3.	Infrastructure requirements:
      - Launch type: AWS Fargate.
      - OS: Linux/X86_64.
      - Task size: 0.5 vCPU and 1 GB RAM (Plenty for a portfolio API).
4.	Container - 1: The App (PHP-FPM)
      - Name: app
      - Image URI: Paste your keepr-app ECR URI (e.g., 123456789012.dkr.ecr.us-east-1.amazonaws.com/keepr-app:latest).
      - Port mappings: Port 9000 (Protocol: TCP).
        - Environment variables: This is where you add your .env content.
            - APP_ENV: production
            - APP_DEBUG: false
            - LOG_CHANNEL: stderr (Crucial so you can see logs in CloudWatch)
            - DB_CONNECTION: pgsql
            - DB_HOST: Your RDS Endpoint
            - DB_PORT:5432
            - DB_DATABASE: keepr
            - DB_USERNAME: postgres
            - DB_PASSWORD: Your DB Password
            - APP_KEY: Your Laravel Key
            - APP_ENV: production

5.	Configuring the Health Check for the App Container
      In your Task Definition, edit the app container and scroll down to the HealthCheck section.
      Because the app container is running PHP-FPM on port 9000 (which doesn't speak HTTP), we can't just "curl" it. The industry standard way to check if PHP-FPM is alive is to use the cgi-fcgi tool or a simple nc (netcat) command to see if the port is open and responding.
      Since we are using Alpine, we can use nc to check port 9000. Add these settings to the app container:
      •	Command: ["CMD-SHELL", "nc -z 127.0.0.1 9000 || exit 1"]
      •	Interval: 30
      •	Timeout: 5
      •	Start period: 60 (This gives Laravel/Composer a minute to warm up)
      •	Retries: 3


In the same container => click “add container” button
6.	Container - 2: The Web (Nginx)
      o	Name: web
      o	Image URI: Paste your keepr-web ECR URI.
      o	Port mappings: Port 80 (Protocol: TCP, Name: http).
      o	Depends on: Add a condition so web only starts after app is Healthy.

Step 3: 🚀 Next Step: The Application Load Balancer (ALB)
Now that you have the Task Definition (with the health check and the sidecars), we need to set up the Application Load Balancer (ALB).
Think of the ALB as the "Receptionist" for your API. It sits at the front door of AWS, takes requests from the internet, and hands them to your Nginx containers.
Let's set up the ALB:
1.	Go to EC2 Dashboard > Load Balancing > Load Balancers.
2.	Click Create load balancer > Application Load Balancer.
3.	Name: keepr-alb.
4.	Scheme: Internet-facing.
5.	IP address type: IPv4.
6.	Network Mapping: Select your Default VPC and all available Subnets (usually 3 or more). This ensures high availability.
      Security Groups for the ALB:
      - Create a new Security Group for the ALB (e.g., keepr-alb-sg).
      - Inbound Rules: Allow HTTP (Port 80) from Anywhere (0.0.0.0/0).
      Listeners and Routing:
      - This is where we tell the ALB where to send the traffic. We need to create a Target Group.
      - Click "Create target group" (not the Add Target Group, this opens a new tab).
      - Target type: IP addresses (Crucial for Fargate).
      - Name: keepr-api-tg.
      - Protocol: HTTP, Port: 80.
      - Health checks: Path: /api/health (or just / if you haven't made a health route yet).
      Once you create the Target Group, go back to the ALB tab, refresh(right next to the input select), and select it
