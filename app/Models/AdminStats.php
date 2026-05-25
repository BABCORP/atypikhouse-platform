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
            'published_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "published"')->fetchColumn(),
            'pending_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "pending"')->fetchColumn(),
            'confirmed_bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "confirmed"')->fetchColumn(),
            'pending_reviews' => (int) $this->db->query('SELECT COUNT(*) FROM reviews WHERE status = "pending"')->fetchColumn(),
            'simulated_revenue' => (float) $this->db->query('SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE payment_status = "test_paid"')->fetchColumn(),
        ];
    }
}
