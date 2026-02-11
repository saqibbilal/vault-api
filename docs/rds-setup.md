**Phase 2: RDS (Database Layer): 🐘 RDS Setup: Step-by-Step**
1. Create a Security Group (The "Firewall")
   Before creating the DB, we need a "shield" for it.
1.	Go to the VPC Dashboard > Security Groups.
2.	Click Create security group.
      o	Name: keepr-rds-sg
      o	Description: Allow PostgreSQL traffic from Backend.
      o	VPC: Select your default VPC.
3.	Inbound Rules:
      o	Add a rule: Type: PostgreSQL (Port 5432).
      o	Source: For now, select My IP (so you can run migrations from your laptop) and later we will add the ECS Security Group here.
4.	Click Create.
2. Create the Database Instance
   Now, go to the RDS Dashboard and click Create database.
   •	Choose a creation method: Standard create.
   •	Engine options: PostgreSQL.
   •	Engine Version: 16.x or 17.x (Latest stable).
   •	Templates: Free Tier (Crucial to avoid charges!).
3. Settings & Credentials
   •	DB instance identifier: keepr-db
   •	Master username: postgres (or your choice).
   •	Master password: Set something strong and save it in your .env immediately.
4. Instance Configuration & Connectivity
   •	Instance configuration: db.t3.micro or db.t4g.micro (Free tier eligible).
   •	Storage: 20GB General Purpose SSD (gp3).
   •	Connectivity:
   o	Public access: Yes (Only for now, so you can push your local schema/migrations up. We will toggle this to 'No' once the app is live).
   o	VPC security group: Choose Existing and select the keepr-rds-sg you just created. Remove the "default" one.
   •	Database port: 5432.
5. Additional Configuration (Don't skip!)
   Scroll down to the bottom:
   •	Initial database name: keepr (This creates the actual DB inside the instance).
   •	Backup: Disable "Enable automated backups" if you want to keep things lightweight for a practice project (though usually recommended in production).
   •	Encryption: Keep enabled.
________________________________________
⏳ The "Waiting Game"
Once you click Create Database, it will take about 5–10 minutes to provision. It will show a status of "Creating" then "Backing up" and finally "Available."
🚀 What to do while you wait:
While AWS is spinning up your hardware, let's prepare your Laravel app's connection. Once the DB is "Available," you will get an Endpoint (it looks like a long URL).
With that, you will have all the needed info to add to your .env file:
# AWS RDS DB
- DB_CONNECTION=pgsql
- DB_HOST=keepr-db.cyx8cq8osb2n.us-east-1.rds.amazonaws.com
- DB_PORT=5432
- DB_DATABASE=keepr
- DB_USERNAME=postgres
- DB_PASSWORD=Castlevanis786
You can connect to AWS RDS db and run migrations using php artisan migrate
