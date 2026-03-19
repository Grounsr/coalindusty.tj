#!/usr/bin/env python3
"""api_server.py — FastAPI backend for Coal Forum registration."""
import sqlite3
import re
from contextlib import asynccontextmanager
from datetime import datetime

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, field_validator


DB_PATH = "registrations.db"


def get_db():
    db = sqlite3.connect(DB_PATH, check_same_thread=False)
    db.row_factory = sqlite3.Row
    return db


def init_db():
    db = get_db()
    db.execute("""
        CREATE TABLE IF NOT EXISTS registrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            organization TEXT NOT NULL,
            position TEXT NOT NULL,
            country TEXT NOT NULL,
            city TEXT DEFAULT '',
            email TEXT NOT NULL,
            phone TEXT DEFAULT '',
            participation_type TEXT NOT NULL DEFAULT 'delegate',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    """)
    db.commit()
    db.close()


@asynccontextmanager
async def lifespan(app):
    init_db()
    yield


app = FastAPI(lifespan=lifespan, title="Coal Forum API")
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)


class RegistrationRequest(BaseModel):
    full_name: str
    organization: str
    position: str
    country: str
    city: str = ""
    email: str
    phone: str = ""
    participation_type: str = "delegate"

    @field_validator("email")
    @classmethod
    def validate_email(cls, v):
        pattern = r"^[^\s@]+@[^\s@]+\.[^\s@]+$"
        if not re.match(pattern, v):
            raise ValueError("Invalid email address")
        return v.strip().lower()

    @field_validator("full_name", "organization", "position", "country")
    @classmethod
    def not_empty(cls, v):
        if not v or not v.strip():
            raise ValueError("Field cannot be empty")
        return v.strip()

    @field_validator("participation_type")
    @classmethod
    def valid_type(cls, v):
        allowed = {"delegate", "speaker", "media"}
        if v not in allowed:
            raise ValueError(f"Must be one of: {', '.join(allowed)}")
        return v


@app.post("/api/register")
def register(data: RegistrationRequest):
    db = get_db()
    try:
        # Check for duplicate email
        existing = db.execute(
            "SELECT id FROM registrations WHERE email = ?", (data.email,)
        ).fetchone()
        if existing:
            raise HTTPException(
                status_code=400,
                detail="This email is already registered."
            )

        cursor = db.execute(
            """INSERT INTO registrations 
               (full_name, organization, position, country, city, email, phone, participation_type)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?)""",
            (
                data.full_name,
                data.organization,
                data.position,
                data.country,
                data.city,
                data.email,
                data.phone,
                data.participation_type,
            ),
        )
        db.commit()
        return {
            "success": True,
            "message": "Registration successful",
            "id": cursor.lastrowid,
        }
    finally:
        db.close()


@app.get("/api/registrations")
def list_registrations():
    db = get_db()
    try:
        rows = db.execute(
            "SELECT * FROM registrations ORDER BY created_at DESC"
        ).fetchall()
        return [dict(row) for row in rows]
    finally:
        db.close()


@app.get("/api/stats")
def stats():
    db = get_db()
    try:
        count = db.execute("SELECT COUNT(*) as cnt FROM registrations").fetchone()
        return {"total_registrations": count["cnt"]}
    finally:
        db.close()


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
