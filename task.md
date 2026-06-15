# TASK.md

# Project Name

VoiceConnect (FRND-style Social Audio Platform)

---

# Project Overview

Build a scalable social audio and video platform similar to FRND.

The platform allows users to:

* Register and login using OTP
* Create profiles
* Match with users
* Perform one-to-one audio calls
* Perform one-to-one video calls
* Join audio rooms
* Send gifts
* Purchase coins
* Earn rewards as hosts
* Withdraw earnings
* Chat in private and group rooms
* Receive notifications
* Participate in referral programs

---

# Technology Stack

## Backend

Laravel 13

Features:

* Sanctum Authentication
* Reverb WebSockets
* Redis
* Horizon
* Queues
* Scheduled Tasks

## Frontend Admin

React Starter Kit (InertiaJS)

## Mobile

React Native

## Realtime

Laravel Reverb

## Voice & Video

Agora SDK

## Database

MySQL 8

## Storage

AWS S3

## Notifications

Firebase FCM

---

# Architecture Rules

Follow clean architecture.

Use:

* Service Classes
* Repository Pattern
* API Resources
* Form Requests
* Events
* Listeners
* Policies

Avoid fat controllers.

Controllers must only coordinate requests.

Business logic belongs in services.

---

# User Roles

## Super Admin

Full access.

## Admin

Limited management access.

## Host

Can create rooms and earn rewards.

## User

Standard application user.

---

# Core Modules

## Authentication Module

Features:

* Mobile OTP Login
* Profile Completion
* Device Tracking
* Sanctum Tokens

Tables:

users
user_devices
otp_logs

---

## Profile Module

Features:

* Avatar
* Nickname
* Bio
* Gender
* Language
* State
* Country

Tables:

profiles

---

## Wallet Module

Features:

* Coin Balance
* Reward Balance
* Transactions

Tables:

wallets
wallet_transactions

---

## Coin Package Module

Features:

* Coin Packages
* Discounts
* Promotional Packs

Tables:

coin_packages

---

## Gift Module

Features:

* Gift Catalog
* Gift Animation URL
* Coin Cost
* Reward Value

Tables:

gifts
gift_transactions

---

## Matchmaking Module

Features:

* Random Match
* Language Match
* Gender Preferences

Matching factors:

* Language
* Gender
* Online Status
* Availability

Tables:

match_requests
matches

---

## Chat Module

Build using Laravel Reverb.

Features:

* Private Chat
* Room Chat
* Message Read Status
* Typing Indicators
* Online Status

Tables:

conversations
conversation_participants
messages
message_reads

Events:

MessageSent
MessageRead
UserTyping

---

## Audio Call Module

Agora Integration.

Features:

* One-to-One Audio Calls

Tables:

call_sessions
call_logs

---

## Video Call Module

Agora Integration.

Features:

* One-to-One Video Calls

Tables:

video_sessions
video_logs

---

## Room Module

Features:

* Public Rooms
* Private Rooms
* Scheduled Rooms

Roles:

* Host
* Speaker
* Listener

Tables:

rooms
room_members

---

## Live Room Chat

Features:

* Realtime Room Chat
* Gifts
* Reactions

Tables:

room_messages

---

## Host Module

Features:

* Host Application
* Approval Workflow
* Earnings Dashboard

Tables:

hosts
host_applications

---

## Rewards Module

Features:

* Reward Tracking
* Reward Conversion

Tables:

reward_transactions

---

## Withdrawal Module

Features:

* Bank Details
* Withdrawal Requests
* Approval Workflow

Tables:

withdrawal_accounts
withdrawal_requests

---

## Referral Module

Features:

* Referral Code
* Referral Tracking

Tables:

referrals

---

## Notification Module

Features:

* Push Notifications
* In App Notifications

Tables:

notifications

---

## Reporting Module

Features:

* User Reports
* Room Reports
* Abuse Reports

Tables:

reports

---

## Moderation Module

Features:

* User Ban
* User Suspension
* Room Closure

Tables:

bans

---

# Reverb Requirements

Implement channels:

PrivateChatChannel
RoomChatChannel
UserPresenceChannel

Support:

* Presence
* Online Users
* Typing Status
* Message Delivery

---

# Admin Dashboard

Build pages:

Dashboard

Users

Hosts

Rooms

Wallet

Transactions

Coin Packages

Gifts

Withdrawals

Reports

Settings

Analytics

---

# Analytics Dashboard

Track:

DAU

MAU

New Users

Coin Purchases

Revenue

Call Duration

Video Duration

Gift Transactions

Top Hosts

Top Rooms

Withdrawal Requests

---

# Database Standards

Use UUIDs for public entities.

Add:

created_by
updated_by

where applicable.

Add indexes on:

user_id
room_id
conversation_id

Use foreign key constraints.

---

# API Standards

Version all APIs.

Prefix:

/api/v1

Use:

API Resources

Form Requests

Policies

Rate Limiting

Sanctum Authentication

---

# Queue Jobs

SendNotificationJob

ProcessGiftTransactionJob

ProcessRewardJob

GenerateAnalyticsJob

CleanupExpiredMatchesJob

---

# Security

Use:

Sanctum

Rate Limiting

Device Tracking

Activity Logs

Input Validation

Authorization Policies

---

# Future Phase 2

Video Rooms

Creator Levels

Agency Management

Leaderboards

Lucky Draw

Events

AI Moderation

Voice Recording Review

Advanced Analytics

Multi Language Localization

---

# Development Priority

Phase 1

Authentication
Profiles
Wallet
Coin Packages
Gifts
Chat
Audio Call
Video Call

Phase 2

Rooms
Hosts
Rewards
Withdrawals

Phase 3

Referrals
Analytics
Moderation

Phase 4

Scaling and Optimization

---

Goal:

Create a scalable production-ready social audio platform capable of supporting 100,000+ users with Laravel 13, Reacct stater kit,  Reverb, Redis, and Agora.
IMPORTANT:

Before generating code:

1. Create all migrations first.
2. Create Eloquent models and relationships.
3. Create service classes.
4. Create API resources.
5. Create form requests.
6. Create controllers.
7. Create tests.
8. Create React admin pages.

Never skip migrations and relationships.
Always generate production-ready code.
Follow Laravel 13 best practices.
Use TypeScript where applicable.


