# 🏆 LevelUp Health Cycle Scoring System

## Overview
The Health Cycle Scoring System rewards users for maintaining healthy alternation between sitting and standing based on Cornell University research and the LINAK 20:10 pattern (20 minutes sitting, 10 minutes standing).

## 🎯 Key Features

### Daily Points Limit
- Users can earn a **maximum of 100 points per day**
- Points reset at midnight based on user's timezone (`last_daily_reset` field)
- Total lifetime points accumulate indefinitely
- Daily progress is displayed in the navbar

### Timezone-Aware Daily Reset
The system uses timezone-aware daily resets to ensure points reset at the correct time for each user:
- `last_daily_reset` field tracks when the user's daily points were last reset
- API accepts `user_date` parameter to handle timezone differences
- Prevents users from gaming the system by changing timezones
- Ensures fair daily limits regardless of user's location

### Scoring Algorithm (LINAK 20:10)

The algorithm calculates a health score (0-100) for each sit-stand cycle:

#### Step 0: Minimum Cycle Check (Anti-Gaming Protection)
- **Minimum Total Duration**: 15 minutes
- Any cycle shorter than 15 minutes total automatically gets **0 points**
- Cycles of **exactly 15 minutes ARE allowed** and will be scored normally
- Prevents gaming the system with tiny cycles (e.g., 2 min sit + 1 min stand)
- Ensures users complete meaningful work periods

#### Step 1: Ratio Accuracy (70% weight)
- **Ideal Ratio**: 2:1 (20 min sitting : 10 min standing)
- Measures how close the user's pattern matches the Cornell research recommendation
- Formula: `1 - |user_ratio - 2| / 2`

#### Step 2: Duration Balance (30% weight)
- **Ideal Total**: ~30 minutes per cycle
- Rewards cycles that last around 30 minutes
- Allows flexibility (25-35 minutes gets full points)
- Formula: `1 - |total_minutes - 30| / 20`

#### Final Score
```
if (total_time < 15 minutes):
    return 0  # Too short, no points

# Cycles of exactly 15 minutes proceed to scoring
health_score = (ratio_score × 0.7 + duration_score × 0.3) × 100
```

### Points Conversion

| Health Score | Points | Feedback | Color |
|-------------|--------|----------|-------|
| 90-100 | +10 pts | 🟢 Perfect! Excellent sit–stand balance | Green |
| 70-89 | +7 pts | 🟡 Good — keep this rhythm going | Yellow |
| 50-69 | +4 pts | 🟠 Fair — try adjusting your times a bit | Orange |
| <50 | 0 pts | 🔴 Too much sitting or too short — no points | Red |

## 📊 Examples

| Sitting | Standing | Ratio | Total | Score | Points | Interpretation |
|---------|----------|-------|-------|-------|--------|----------------|
| 20 min | 10 min | 2.0 | 30 min | 100 | +10 | 🟢 Perfect balance |
| 25 min | 10 min | 2.5 | 35 min | 85 | +7 | 🟡 Good — slightly long sitting |
| 30 min | 5 min | 6.0 | 35 min | 40 | 0 | 🔴 Too much sitting |
| 15 min | 15 min | 1.0 | 30 min | 70 | +7 | 🟡 Balanced but too much standing |
| 10 min | 5 min | 2.0 | 15 min | 78 | +7 | 🟡 Good — minimum cycle time, perfect ratio |
| 2 min | 1 min | 2.0 | 3 min | 0 | 0 | 🔴 Too short — cycle incomplete (< 15 min) |

**Note**: Any cycle under 15 minutes total automatically scores 0. Cycles of exactly 15 minutes are allowed and will be scored normally.

## 🔧 API Endpoints

### Complete a Health Cycle
```
POST /api/health-cycle/complete
```
**Body:**
```json
{
  "sitting_minutes": 20,
  "standing_minutes": 10,
  "cycle_number": 5,
  "user_date": "2025-10-15"
}
```

**Response:**
```json
{
  "success": true,
  "message": "You earned 10 points!",
  "health_score": 100,
  "points_earned": 10,
  "daily_points": 45,
  "total_points": 1250,
  "todays_cycles": 3,
  "feedback": "🟢 Perfect! Excellent sit–stand balance.",
  "color": "green",
  "daily_limit_reached": false,
  "user_date": "2025-10-15"
}
```

### Get Points Status
```
GET /api/health-cycle/points-status?user_date=2025-10-15
```

**Response:**
```json
{
  "total_points": 1250,
  "daily_points": 45,
  "todays_cycles": 3,
  "can_earn_more": true,
  "points_remaining_today": 55,
  "user_date": "2025-10-15"
}
```

### Get Cycle History
```
GET /api/health-cycle/history?limit=10
```

## 💾 Database Tables

### users table
- `total_points` - Lifetime total points accumulated
- `daily_points` - Points earned today (resets daily)
- `last_points_date` - Last date when points were earned (for legacy daily reset)
- `last_daily_reset` - Date when daily points were last reset (timezone-aware)

### health_cycles table
- `user_id` - Foreign key to users
- `sitting_minutes` - Duration of sitting session
- `standing_minutes` - Duration of standing session
- `cycle_number` - Sequential cycle number
- `health_score` - Calculated score (0-100)
- `points_earned` - Points awarded for this cycle
- `completed_at` - Timestamp of completion

## 🔐 Authentication

All API endpoints require authentication via Laravel's `auth` middleware. Guests can use the timer but won't earn points.

## 🎓 Research Reference

This system is based on:
- **Cornell University** ergonomics research
- **LINAK** 20:10 sit-stand pattern
