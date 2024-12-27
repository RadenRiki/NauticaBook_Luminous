import React, { useState } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Settings, Lock, Route } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { LineChart, Line, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { Users, Ship, Package, DollarSign } from 'lucide-react';

const DashboardPage = () => {
  // Sample data - In real app, this would come from your API/database
  const monthlyData = [
    { month: 'Jan', ferry: 65, cargo: 28 },
    { month: 'Feb', ferry: 59, cargo: 32 },
    { month: 'Mar', ferry: 80, cargo: 41 },
    { month: 'Apr', ferry: 81, cargo: 37 },
    { month: 'May', ferry: 56, cargo: 25 },
    { month: 'Jun', ferry: 55, cargo: 30 },
    { month: 'Jul', ferry: 40, cargo: 22 }
  ];

  const recentBookings = [
    { id: 1, type: 'Ferry', route: 'Merak-Bakauheni', date: '2024-01-15', status: 'Completed' },
    { id: 2, type: 'Cargo', route: 'Ketapang-Gilimanuk', date: '2024-01-14', status: 'In Progress' },
    { id: 3, type: 'Ferry', route: 'Bakauheni-Merak', date: '2024-01-14', status: 'Pending' }
  ];

  return (
    <div className="p-8">
      <Tabs defaultValue="overview" className="space-y-4">
        <TabsList>
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="routes">Route Management</TabsTrigger>
          <TabsTrigger value="settings">Settings</TabsTrigger>
        </TabsList>

        <TabsContent value="overview">
          <h1 className="text-3xl font-bold mb-8">Dashboard Overview</h1>
      
      {/* Stats Overview */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center space-x-4">
              <div className="p-3 bg-blue-100 rounded-full">
                <Users className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500">Total Users</p>
                <h3 className="text-2xl font-bold">2,543</h3>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center space-x-4">
              <div className="p-3 bg-green-100 rounded-full">
                <Ship className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500">Ferry Bookings</p>
                <h3 className="text-2xl font-bold">436</h3>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center space-x-4">
              <div className="p-3 bg-purple-100 rounded-full">
                <Package className="h-6 w-6 text-purple-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500">Cargo Shipments</p>
                <h3 className="text-2xl font-bold">215</h3>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center space-x-4">
              <div className="p-3 bg-yellow-100 rounded-full">
                <DollarSign className="h-6 w-6 text-yellow-600" />
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500">Revenue</p>
                <h3 className="text-2xl font-bold">Rp 452.8M</h3>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <Card>
          <CardHeader>
            <CardTitle>Monthly Bookings</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={monthlyData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="month" />
                  <YAxis />
                  <Tooltip />
                  <Legend />
                  <Line type="monotone" dataKey="ferry" stroke="#2563eb" name="Ferry Bookings" />
                  <Line type="monotone" dataKey="cargo" stroke="#7c3aed" name="Cargo Shipments" />
                </LineChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Revenue Distribution</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={monthlyData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="month" />
                  <YAxis />
                  <Tooltip />
                  <Legend />
                  <Bar dataKey="ferry" fill="#2563eb" name="Ferry Revenue" />
                  <Bar dataKey="cargo" fill="#7c3aed" name="Cargo Revenue" />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Recent Bookings Table */}
      <Card>
        <CardHeader>
          <CardTitle>Recent Bookings</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b">
                  <th className="py-3 px-4 text-left">Booking ID</th>
                  <th className="py-3 px-4 text-left">Type</th>
                  <th className="py-3 px-4 text-left">Route</th>
                  <th className="py-3 px-4 text-left">Date</th>
                  <th className="py-3 px-4 text-left">Status</th>
                </tr>
              </thead>
              <tbody>
                {recentBookings.map((booking) => (
                  <tr key={booking.id} className="border-b">
                    <td className="py-3 px-4">#{booking.id}</td>
                    <td className="py-3 px-4">{booking.type}</td>
                    <td className="py-3 px-4">{booking.route}</td>
                    <td className="py-3 px-4">{booking.date}</td>
                    <td className="py-3 px-4">
                      <span className={`px-2 py-1 rounded-full text-xs ${
                        booking.status === 'Completed' ? 'bg-green-100 text-green-800' :
                        booking.status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
                        'bg-yellow-100 text-yellow-800'
                      }`}>
                        {booking.status}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
        </TabsContent>

        <TabsContent value="routes">
          <div className="space-y-6">
            <div className="flex justify-between items-center">
              <h2 className="text-3xl font-bold">Route Management</h2>
              <Button className="bg-blue-600 hover:bg-blue-700">
                Add New Route
              </Button>
            </div>

            <Card>
              <CardHeader>
                <CardTitle>Fare Management</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b">
                        <th className="py-3 px-4 text-left">Route</th>
                        <th className="py-3 px-4 text-left">Service</th>
                        <th className="py-3 px-4 text-left">Type</th>
                        <th className="py-3 px-4 text-left">Category</th>
                        <th className="py-3 px-4 text-left">Price</th>
                        <th className="py-3 px-4 text-left">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr className="border-b">
                        <td className="py-3 px-4">Merak-Bakauheni</td>
                        <td className="py-3 px-4">Regular</td>
                        <td className="py-3 px-4">Pejalan Kaki</td>
                        <td className="py-3 px-4">Dewasa</td>
                        <td className="py-3 px-4">
                          <Input 
                            type="number" 
                            defaultValue="22700"
                            className="w-32"
                          />
                        </td>
                        <td className="py-3 px-4">
                          <div className="flex gap-2">
                            <Button size="sm" variant="outline">Save</Button>
                            <Button size="sm" variant="destructive">Delete</Button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="settings">
          <div className="space-y-6">
            <h2 className="text-3xl font-bold">Admin Settings</h2>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Lock className="w-5 h-5" />
                    Change Password
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <form className="space-y-4">
                    <div>
                      <label className="text-sm font-medium">Current Password</label>
                      <Input type="password" />
                    </div>
                    <div>
                      <label className="text-sm font-medium">New Password</label>
                      <Input type="password" />
                    </div>
                    <div>
                      <label className="text-sm font-medium">Confirm New Password</label>
                      <Input type="password" />
                    </div>
                    <Button className="w-full">Update Password</Button>
                  </form>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Settings className="w-5 h-5" />
                    General Settings
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <form className="space-y-4">
                    <div>
                      <label className="text-sm font-medium">Admin Email</label>
                      <Input type="email" defaultValue="admin@nauticabook.com" />
                    </div>
                    <div>
                      <label className="text-sm font-medium">Notification Preferences</label>
                      <div className="flex items-center space-x-2 mt-2">
                        <input type="checkbox" id="emailNotif" className="rounded" />
                        <label htmlFor="emailNotif">Email Notifications</label>
                      </div>
                    </div>
                    <Button className="w-full">Save Settings</Button>
                  </form>
                </CardContent>
              </Card>
            </div>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default DashboardPage;