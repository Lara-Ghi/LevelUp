# 🏆 LevelUp Health Cycle Scoring System

## Overview
The Health Cycle Scoring System rewards users for maintaining healthy alternation between sitting and standing based on Cornell University research and the LINAK 20:10 pattern (20 minutes sitting, 10 minutes standing).

## 🎯 Key Features

### Daily Points Limit
- Users can earn a **maximum of 100 points per day**
- Points reset at midnight
- Total lifetime points accumulate indefinitely
- Daily progress is displayed in the navbar

### Scoring Algorithm (LINAK 20:10)

The algorithm calculates a health score (0-100) for each sit-stand cycle:

#### Step 0: Minimum Cycle Check (Anti-Gaming Protection)
- **Minimum Total Duration**: 15 minutes
- Any cycle shorter than 15 minutes total automatically gets **0 points**
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
| 30 min | 5 min | 6.0 | 35 min | 40 | 0 | 🟠 Too much sitting |
| 15 min | 15 min | 1.0 | 30 min | 70 | +7 | 🟡 Balanced but too much standing |
| 10 min | 5 min | 2.0 | 15 min | 0 | 0 | � Cycle exactly at minimum (barely qualifies) |
| 2 min | 1 min | 2.0 | 3 min | 0 | 0 | 🔴 Too short — cycle incomplete (< 15 min) |

**Note**: Any cycle under 15 minutes total automatically scores 0, preventing gaming the system with tiny cycles.

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
  "cycle_number": 5
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
  "feedback": "🟢 Perfect! Excellent sit–stand balance.",
  "color": "green",
  "daily_limit_reached": false
}
```

### Get Points Status
```
GET /api/health-cycle/points-status
```

**Response:**
```json
{
  "total_points": 1250,
  "daily_points": 45,
  "can_earn_more": true,
  "points_remaining_today": 55
}
```

### Get Cycle History
```
GET /api/health-cycle/history?limit=10
```

## 💾 Database Tables

### users table (modified)
- `total_points` - Lifetime total points
- `daily_points` - Points earned today
- `last_points_date` - Last date points were earned

### health_cycles table (new)
- `user_id` - Foreign key to users
- `sitting_minutes` - Duration of sitting session
- `standing_minutes` - Duration of standing session
- `cycle_number` - Sequential cycle number
- `health_score` - Calculated score (0-100)
- `points_earned` - Points awarded for this cycle
- `completed_at` - Timestamp of completion

## 🚀 Setup

1. Run migrations:
```bash
php artisan migrate
```

2. The system automatically integrates with the Desk Timer
3. Points are awarded automatically when a cycle completes
4. No user action required - just use the timer!

## 📱 Frontend Integration

The points system is integrated into the Desk Timer:
- **Navbar**: Displays total points and daily progress
- **Auto-submission**: Cycles are automatically submitted when completed
- **Notifications**: Real-time feedback shows score, points, and health tips
- **Graceful degradation**: Works without authentication (doesn't break for guests)

## 🔐 Authentication

All API endpoints require authentication via Laravel's `auth` middleware. Guests can use the timer but won't earn points.

## 🎓 Research Reference

This system is based on:
- **Cornell University** ergonomics research
- **LINAK** 20:10 sit-stand pattern
- Focus on sustainable desk work habits
- Evidence-based health recommendations

## 📈 Future Enhancements

Potential additions:
- Streak bonuses (5+ perfect cycles in a row)
- Leaderboards
- Weekly/monthly statistics
- Custom health goals
- Achievement badges
- Social sharing features
