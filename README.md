# TechFive_XISD
XISD5319

# Donor Admin App

**Donate • Support • Make a Difference**

A role-based Android application developed to support donor management, fundraising campaigns, donation tracking, communication and administrative reporting for a donation-driven organisation.
---

## Project Information

| Item | Details |
|---|---|
| Project name | Donor Admin App |
| Organisation | Save the Children South Africa NPO |
| Platform | Android mobile application and website |
| Android development environment | Android Studio |
| Programming languages | Kotlin and XML |
| Authentication | Firebase Authentication, including Google Sign-In where configured |
| Mobile application database | Cloud Firestore |
| Website database | SQL |
| Source control | Git and GitHub |
| Automated workflow | GitHub Actions |

## Contents

1. [Project Purpose](#1-project-purpose)
2. [Problem Statement](#2-problem-statement)
3. [Target Users and Roles](#3-target-users-and-roles)
4. [Application Features](#4-application-features)
5. [Design Considerations](#5-design-considerations)
6. [Architecture and Technologies](#6-architecture-and-technologies)
7. [Authentication and Security](#7-authentication-and-security)
8. [Database Structure](#8-database-structure)
9. [How the Application Works](#9-how-the-application-works)
10. [Website and Mobile Application](#10-website-and-mobile-application)
11. [Installation and Setup](#12-installation-and-setup)
12. [Testing](#13-testing)
13. [Release Notes](#14-release-notes)
14. [Limitations and Future Improvements](#15-limitations-and-future-improvements)
15. [Video Presentation](#17-video-presentation)
16. [Acknowledgements](#18-acknowledgements)

---

## 1. Project Purpose

The Donor Admin App provides a digital platform for managing donors, fundraising campaigns, donations and organisational communication.

The application aims to make donation-related activities more accessible and organised by allowing donors to explore fundraising campaigns, record contributions and monitor their donation histories. Staff and administrators can manage campaigns, review donations, communicate with donors and access relevant reports.

The mobile application extends the organisation's digital services to Android devices, while the website provides additional functionality for donor and administrative management.

The overall goal is to improve record keeping, support transparency, simplify administrative processes and strengthen communication between the organisation and its donors.

## 2. Problem Statement

Managing donations through manual processes or disconnected systems can result in inaccurate records, difficulties tracking contributions, delayed communication and limited visibility of fundraising progress.

The system addresses these challenges by providing digital tools for recording donations, managing fundraising campaigns, reviewing contributions and communicating with donors.

## 3. Target Users and Roles

The application supports three main user roles.

| Role | Description and permissions |
|---|---|
| **Donor (`USER`)** | Can browse active campaigns, record donations, view personal donation history, submit enquiries, view replies and announcements, and manage personal profile information. |
| **Staff (`STAFF`)** | Can create and manage campaigns, view donations, review pending donations, respond to enquiries, publish announcements and access reports. |
| **Administrator (`ADMIN`)** | Has staff permissions, together with additional functionality for donor management and approving or rejecting staff applications. |

### Account registration and access

- Donors register with an active account.
- Staff registrations require administrator approval before access is granted.
- Administrator accounts are provisioned manually.
- Users are given access to functionality according to their assigned roles and account statuses.

## 4. Application Features

### 4.1 User registration and authentication
- Account registration and login.
- Email and password authentication.
- Google Sign-In option.
- Password recovery.
- Role-based access to application functionality.
- Logout and account session management.

### 4.2 Campaign management
- Create and manage fundraising campaigns.
- Display campaign descriptions and fundraising targets.
- Show amounts raised and campaign progress.
- Allow donors to explore available fundraising opportunities.

### 4.3 Donation management
- Select a fundraising campaign.
- Enter a donation amount.
- Select an available payment method.
- View personal donation history.
- Allow authorised staff to review pending donations.

### 4.4 Donation verification
- Review pending cash and EFT donation records.
- Approve or reject submitted donations.
- Maintain donation verification statuses.
- Record relevant verification information.

### 4.5 Donor communication
- Submit enquiries to the organisation.
- View previous conversations.
- Receive responses from staff.
- Access organisational announcements.

### 4.6 Administrative functionality
- View donor information.
- Monitor recorded donor contributions.
- Approve or reject staff applications.
- Review fundraising and donation reports.
- Monitor campaign performance.

### 4.7 Profile and Settings
- View account information.
- Update permitted personal details.
- Manage account security.
- Request password resets.
- Log out securely.

## 5. Design Considerations

The application was designed with usability, consistency, accessibility and security in mind.

| Design principle | Application |
|---|---|
| Usability | Clear labels, familiar Android controls and straightforward navigation. |
| Consistency | Reusable card, button, input and heading styles across screens. |
| Visual hierarchy | Clear headings and primary actions help users identify important information. |
| Responsive layouts | Scrollable forms and layouts support different device sizes. |
| User feedback | Validation messages, loading indicators, empty states, Toast messages and confirmation dialogs provide feedback. |
| Security | Authentication and role-based access restrict functionality to authorised users. |
| Privacy | Donors should only access their own donation records and conversations. |
| Maintainability | Kotlin Activities and XML layouts separate application logic from screen presentation. |
| Accessibility | Readable labels, appropriate contrast and touch-friendly controls are considered. Further device-level accessibility testing is recommended. |

The website and Android application use a consistent organisational identity to provide users with a recognisable experience across the two platforms.

## 6. Architecture and Technologies

### Technologies used

| Technology | Purpose |
|---|---|
| Android Studio | Development environment for the Android application. |
| Kotlin | Application logic, validation, navigation and backend operations. |
| XML | Android screen layouts and interface styling. |
| AndroidX and Material Components | Android compatibility and interface components. |
| Firebase Authentication | User registration, login, identity management and password recovery. |
| Google Sign-In | Alternative authentication using a Google account, where configured. |
| Cloud Firestore | Stores Android application information, including profiles, campaigns, donations and messages. |
| SQL | Database technology used by the website. |
| Git | Tracks source-code changes and development history. |
| GitHub | Hosts the source code and project documentation. |
| GitHub Actions | Supports automated build and testing workflows. |

### Android application architecture

The Android application follows a structure in which XML provides the user interface, Kotlin manages application behaviour, and Firebase provides authentication and cloud data storage.

             Android XML Interface
                       |
                       v
              Kotlin Activities
                       |
          +------------+------------+
          |                         |
          v                         v
 Firebase Authentication     Cloud Firestore
          |                         |
          v                         v
   User identity and          Profiles, campaigns,
   authentication             donations and messages
                                    |
                                    v
                         Firestore Security Rules

**Website and mobile application distinction:** The website uses its SQL database, while the Android application uses Firebase Authentication and Cloud Firestore. These should not be described as one fully integrated database system unless a working API or other integration has been implemented and tested.

## 7. Authentication and Security

Authentication verifies the identity of a user before access is granted to the application.

### Email and password authentication

Firebase Authentication supports account registration and email/password login. It also manages authenticated user identities, logout and password-reset functionality.

### Google Sign-In

The login interface provides a Google Sign-In option, allowing users to authenticate using their Google accounts when the Google authentication provider is enabled and correctly configured.

Google Sign-In provides an alternative authentication method. However, having Google Sign-In on both the website and Android application does not, by itself, prove that a single authenticated session is shared across both platforms.

**SSO verification:** To claim full cross-platform Single Sign-On, the same identity provider and compatible authentication/session handling must be implemented and tested across the website and Android application. The report should distinguish successful Google authentication from verified shared-session SSO.

### Role-based access

After authentication, the application checks the user's profile and assigned role/status to determine which features are available.

| Account type | Access |
|---|---|
| Donor | Donor functionality |
| Approved Staff | Staff functionality |
| Administrator | Administrative functionality |

Firestore Security Rules provide backend access restrictions. Hiding a button or screen in the application is not sufficient security on its own.

## 8. Database Structure

The Android application uses Cloud Firestore, a cloud-based NoSQL document database.

| Collection/document | Information stored |
|---|---|
| `users/{uid}` | User identity, name, email, role, status and contact information. |
| `campaigns/{campaignId}` | Campaign title, description, fundraising goal, amount raised and status. |
| `donations/{donationId}` | Donor, campaign, amount, payment method, status and verification information. |
| `donorMessages/{messageId}` | Donor enquiries, sender information, subject and message details. |
| `donorMessages/{messageId}/replies/{replyId}` | Replies, sender role, message content and creation time. |
| `announcements/{announcementId}` | Announcement subject, message, audience and publication information. |

Donation records can have statuses such as:

- `CONFIRMED`
- `PENDING_VERIFICATION`
- `REJECTED`

The website uses its SQL database for its own records. Database consistency and cross-platform data sharing should be verified if the two platforms are expected to use the same information.

## 9. How the Application Works

1. **Registration:** A new user creates an account using the registration form.
2. **Authentication:** The user signs in using email/password or the available Google Sign-In option.
3. **Role verification:** The application checks the user's assigned role and account status.
4. **Dashboard:** The user is directed to the appropriate dashboard.
5. **Campaigns:** Available fundraising campaigns are displayed.
6. **Donations:** Donors select a campaign, enter a contribution amount and submit their donation details.
7. **Verification:** Authorised staff review donations requiring approval.
8. **Communication:** Donors submit enquiries and can view replies and announcements.
9. **Reports:** Staff and administrators access available donation and campaign summaries.
10. **Profile and logout:** Users manage permitted account details and can end their session.

## 10. Website and Mobile Application

The project consists of a website and an Android mobile application.

### Website

The website provides administrative and donor-management functionality through pages such as:

- User registration and login.
- Administrator dashboard.
- Donor management.
- Campaign creation and management.
- Donation recording and history.
- Cash deposit approvals.
- Donor messaging.
- Staff application approvals.
- Financial reports.
- User account settings.

### Android mobile application

The Android application provides mobile access to the system through screens such as:

- Login.
- Create Account.
- Donor Dashboard.
- Donations.
- Fundraising Campaigns.
- Messages and Announcements.
- Settings and Profile Management.

Screenshots of the website and mobile application should be included in the final project report with clear figure numbers, titles and explanations.

## 11. GitHub and GitHub Actions

### GitHub

GitHub was used to host the Android source code and support version control and project documentation.

It provides:
- A central repository for the complete project source code.
- Commit history showing development changes.
- Branches for managing separate development tasks.
- Collaboration and code review through pull requests.
- Project documentation through the README file.
- Version tracking through tags and releases.

### GitHub Actions

GitHub Actions provides automated workflows that can build and test the Android application when changes are pushed to the repository or submitted through pull requests.

A typical Android workflow includes:

1. Checking out the repository.
2. Setting up the Java development environment.
3. Configuring the Android SDK.
4. Building the Android application.
5. Running available automated tests.
6. Reporting whether the workflow succeeded or failed.

Example workflow configuration:

```yaml
name: Android CI

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  build:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout repository
        uses: actions/checkout@v4

      - name: Set up Java
        uses: actions/setup-java@v4
        with:
          distribution: temurin
          java-version: "17"
          cache: gradle

      - name: Set up Android SDK
        uses: android-actions/setup-android@v3

      - name: Build debug APK
        run: ./gradlew assembleDebug

      - name: Run unit tests
        run: ./gradlew test
```
This is an example workflow. The Java version and configuration must match the project's Gradle requirements. The actual workflow file and GitHub Actions run history should be checked before reporting a successful build or test.

## 12. Installation and Setup

### Requirements

- Android Studio.
- A compatible Java Development Kit.
- Android SDK and an emulator or Android device.
- Access to the project's Firebase configuration.

### Setup instructions

1. Clone the GitHub repository:

   ```bash
   git clone <YOUR_REPOSITORY_URL>
   ```

2. Open the downloaded project in Android Studio.
3. Configure the Firebase project.
4. Place the appropriate `google-services.json` file in the Android app module.
5. Enable the required Firebase Authentication providers, including Email/Password and Google where used.
6. Configure Cloud Firestore and deploy the required Security Rules.
7. Sync the Gradle project.
8. Run the application on an emulator or compatible Android device.

**Important:** Do not upload private credentials, service-account keys or sensitive donor information to GitHub.

## 13. Testing

Testing is necessary to confirm that the application behaves as expected and that users can complete their assigned tasks.

The following areas should be tested and the actual results recorded before submission.

| Test area | Expected result |
|---|---|
| Donor registration | A valid donor account is created successfully. |
| Staff registration | A staff application is created with pending approval status. |
| Email/password login | Valid credentials authenticate the user. |
| Google Sign-In | A configured Google account can authenticate successfully. |
| Role-based access | Users can access only the features permitted by their roles. |
| Campaign management | Authorised staff can create and manage campaigns. |
| Donation submission | Valid donation details are recorded. |
| Donation verification | Authorised staff can approve or reject pending records. |
| Messaging | Donors can submit enquiries and view available replies. |
| Announcements | Published announcements can be viewed by the intended audience. |
| Profile management | Users can update permitted personal information. |
| Password recovery | Users can access the password-reset process. |
| Logout | The authenticated session ends and the user returns to the login screen. |

Include actual test outcomes, screenshots or test reports in the final submission. Do not mark a test as passed unless it has been performed.

## 14. Release Notes

The following release notes describe the intended progression from the prototype to the current demonstration version. Confirm the version numbers, dates and completed features against the actual GitHub commit history before publishing.

### Version 2.0.0 — Enhanced Donor Admin App

**Major features and improvements**

- Introduced role-based access for Donors, Staff and Administrators.
- Added donor registration and staff registration with administrator approval.
- Added campaign creation and campaign progress tracking.
- Introduced campaign-linked donation recording.
- Added donation history and donation verification workflows.
- Introduced pending, confirmed and rejected donation statuses.
- Added donor enquiries and staff/admin replies.
- Added organisational announcements.
- Added reporting functionality for donation and campaign activity.
- Introduced donor management and staff application approval.
- Added profile management and password recovery.
- Added Google Sign-In as an alternative authentication option, subject to provider configuration.
- Improved interface consistency, input validation and user feedback.
- Expanded project documentation and testing guidance.

**Innovative features introduced**

- Role-based dashboards that provide different functionality according to the user's responsibilities.
- Staff approval functionality to support controlled access to staff features.
- Campaign-linked donations that associate contributions with specific fundraising initiatives.
- Donation verification to support the review of pending contributions.
- Two-way donor communication through enquiries and replies.
- Administrative reporting and donor contribution monitoring.

**Known limitations**

- Card payment is a demonstration and does not process real payments.
- EFT proof selection does not upload the actual document in this version.
- Production payment processing and additional backend safeguards require further implementation.

### Version 1.0.0 — Initial Prototype

- Introduced the initial Android application structure.
- Created basic login and registration interfaces.
- Established initial campaign and donation functionality.
- Introduced the early application navigation and screen layouts.
- Established the initial Firebase integration.

## 15. Limitations and Future Improvements

Although the application provides several important donation-management features, further development is required before production deployment.

Future improvements include:

- Integrating a secure payment gateway for real card transactions.
- Enabling secure EFT proof uploads.
- Strengthening financial transaction validation and backend safeguards.
- Reviewing Firebase Security Rules before handling real donor information.
- Expanding automated unit and instrumentation test coverage.
- Introducing push notifications for important updates and messages.
- Improving accessibility and testing on different Android devices.
- Implementing and verifying cross-platform authentication if shared-session SSO is required.
- Establishing and testing API integration between the website and Android application if shared data is required.
- Improving reporting and data export functionality.

## 17. Video Presentation

**Video presentation:** https://youtu.be/d33yeXhTG0U

## 18. Acknowledgements

The Donor Admin App was developed using Android Studio, Kotlin, XML, Firebase and GitHub. The project demonstrates the use of mobile application development, authentication, cloud database management, version control and software testing to address practical donor-management needs.

**Together, we can make a difference. **
