<?php

namespace App\Models;

use App\Core\Model;

final class AdminStats extends Model
{
    public function dashboard(): array
    {
        return [
            'users' => (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'owners' => (int) $this->db->query('SELECT COUNT(*) FROM users WHERE role = "owner"')->fetchColumn(),
            'tenants' => (int) $this->db->query('SELECT COUNT(*) FROM users WHERE role = "tenant"')->fetchColumn(),
            'admins' => (int) $this->db->query('SELECT COUNT(*) FROM users WHERE role = "admin"')->fetchColumn(),
            'published_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "published"')->fetchColumn(),
            'pending_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "pending"')->fetchColumn(),
            'rejected_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "rejected"')->fetchColumn(),
            'bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
            'pending_bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "pending_payment"')->fetchColumn(),
            'confirmed_bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "confirmed"')->fetchColumn(),
            'cancelled_bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "cancelled"')->fetchColumn(),
            'pending_reviews' => (int) $this->db->query('SELECT COUNT(*) FROM reviews WHERE status = "pending"')->fetchColumn(),
            'unread_messages' => (int) $this->db->query('SELECT COUNT(*) FROM contact_messages WHERE status = "new"')->fetchColumn(),
            'simulated_revenue' => (float) $this->db->query('SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE payment_status = "test_paid"')->fetchColumn(),
        ];
    }
}
