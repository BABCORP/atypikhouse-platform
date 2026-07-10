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
            'pending_users' => (int) $this->db->query('SELECT COUNT(*) FROM users WHERE status = "pending"')->fetchColumn(),
            'suspended_users' => (int) $this->db->query('SELECT COUNT(*) FROM users WHERE status = "suspended"')->fetchColumn(),
            'published_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "published"')->fetchColumn(),
            'pending_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "pending"')->fetchColumn(),
            'pending_property_changes' => (int) $this->db->query('SELECT COUNT(*) FROM property_change_requests WHERE status = "pending"')->fetchColumn(),
            'rejected_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "rejected"')->fetchColumn(),
            'paused_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "paused"')->fetchColumn(),
            'deleted_properties' => (int) $this->db->query('SELECT COUNT(*) FROM properties WHERE status = "deleted"')->fetchColumn(),
            'bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
            'pending_bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "pending_admin"')->fetchColumn(),
            'confirmed_bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "confirmed"')->fetchColumn(),
            'cancelled_bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "cancelled"')->fetchColumn(),
            'completed_bookings' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "completed"')->fetchColumn(),
            'pending_payments' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE payment_status = "not_paid"')->fetchColumn(),
            'paid_payments' => (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE payment_status = "test_paid"')->fetchColumn(),
            'failed_payments' => (int) $this->db->query('SELECT COUNT(*) FROM payments WHERE status = "test_failed"')->fetchColumn(),
            'pending_reviews' => (int) $this->db->query('SELECT COUNT(*) FROM reviews WHERE status = "pending"')->fetchColumn(),
            'published_reviews' => (int) $this->db->query('SELECT COUNT(*) FROM reviews WHERE status = "published"')->fetchColumn(),
            'rejected_reviews' => (int) $this->db->query('SELECT COUNT(*) FROM reviews WHERE status = "rejected"')->fetchColumn(),
            'unread_messages' => (int) $this->db->query('SELECT COUNT(*) FROM contact_messages WHERE status = "new"')->fetchColumn(),
            'read_messages' => (int) $this->db->query('SELECT COUNT(*) FROM contact_messages WHERE status = "read"')->fetchColumn(),
            'processed_messages' => (int) $this->db->query('SELECT COUNT(*) FROM contact_messages WHERE status = "processed"')->fetchColumn(),
            'simulated_revenue' => (float) $this->db->query('SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE payment_status = "test_paid"')->fetchColumn(),
        ];
    }
}
