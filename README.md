# BoiGhor
BoiGhor is a web-based book selling platform that allows users to explore, review and purchase books.The platform enables seamless browsing, purchasing, and reviewing of books, while integrating an intelligent Machine Learning–based Review Manipulation Prevention System to protect users from fake, biased or malicious reviews.
Our system ensures that only genuine, trustworthy feedback is displayed, enhancing credibility and overall user confidence.
<br>
**Core Features**<br>
User-Focused Features<br>

User authentication & role-based access control (Admin/User)<br>
Modern and responsive UI/UX for easy browsing on any device<br>
Personalized book exploration with category browsing, search and filtering<br>
Secure cart and checkout system<br>
Ability to submit reviews and comments<br>
Review Manipulation Prevention<br>
A key highlight of Boighor is its intelligent review protection system, powered by modern machine-learning techniques.
<br>
**ML-Driven Detection**<br>
All reviews are automatically analyzed using an ML classifier (TF-IDF + Logistic Regression or Transformer-based model)<br>
The API returns a score (0–1) representing the probability of manipulation
<br>
**Automated Moderation Workflow**<br>
Score ≥ 0.80 → Auto-Rejected (highly suspicious/fake)<br>
0.50 ≤ Score < 0.80 → Flagged for Admin Review<br>
Score < 0.50 → Auto-Approved
<br>
**Admin Notifications**<br>
Admins receive instant alerts for every flagged/rejected comment<br>
Full review context displayed for manual verification
<br>
**Admin Management Dashboard**<br>
Administrators have access to comprehensive tools to manage the platform:<br>

Book management<br>
User management<br>
Order tracking<br>
Moderation of flagged reviews<br>
Ability to approve/reject reviews manually<br>
User Trust Score control & user blocking<br>
Audit logs for transparency
<br>
**Technical Highlights**<br>
Backend<br>
PHP (Core application logic)<br>
MySQL (Database management)<br>
FastAPI (Python) microservice for ML inference
<br>
**ML Integration**<br>
Pretrained model served via REST API<br>
PHP communicates with ML service
<br>
**Security**<br>
Password hashing (bcrypt)<br>
Role-based authentication<br>
Encrypted ML communication<br>
Admin-controlled moderation<br>
