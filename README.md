# BoiGhor
BoiGhor is a web-based book selling platform that allows users to explore, review and purchase books.The platform enables seamless browsing, purchasing, and reviewing of books, while integrating an intelligent Machine Learning–based Review Manipulation Prevention System to protect users from fake, biased or malicious reviews.
Our system ensures that only genuine, trustworthy feedback is displayed, enhancing credibility and overall user confidence.
<br>
**Core Features**
User-Focused Features

User authentication & role-based access control (Admin/User)
Modern and responsive UI/UX for easy browsing on any device
Personalized book exploration with category browsing, search and filtering
Secure cart and checkout system
Ability to submit reviews and comments

Review Manipulation Prevention
A key highlight of Boighor is its intelligent review protection system, powered by modern machine-learning techniques.
<br>
**ML-Driven Detection**
All reviews are automatically analyzed using an ML classifier (TF-IDF + Logistic Regression or Transformer-based model)
The API returns a score (0–1) representing the probability of manipulation
<br>
**Automated Moderation Workflow**
Score ≥ 0.90 → Auto-Rejected (highly suspicious/fake)
0.60 ≤ Score < 0.90 → Flagged for Admin Review
Score < 0.60 → Auto-Approved
<br>
**Admin Notifications**
Admins receive instant alerts for every flagged/rejected comment
Full review context displayed for manual verification
<br>
**Admin Management Dashboard**
Administrators have access to comprehensive tools to manage the platform:

Book management
User management
Order tracking
Moderation of flagged reviews
Ability to approve/reject reviews manually
User Trust Score control & user blocking
Audit logs for transparency
<br>
**Technical Highlights**
Backend
PHP (Core application logic)
MySQL (Database management)
FastAPI (Python) microservice for ML inference
<br>
**ML Integration**
Pretrained model served via REST API
PHP communicates with ML service using secure JSON (HTTPS)
<br>
**Security**
Password hashing (bcrypt)
Role-based authentication
Encrypted ML communication
Admin-controlled moderation
