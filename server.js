import { createServer } from 'http';
import { Server } from 'socket.io';
import Redis from 'ioredis';

const httpServer = createServer();
const io = new Server(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
    },
});

// Redis instance
const redis = new Redis();

// Listen for Laravel events from Redis
redis.psubscribe("private-message.*", (err, count) => {
    if (err) {
        console.error("Failed to subscribe:", err.message);
    } else {
        console.log(`Subscribed to ${count} channels.`);
    }
});

redis.on("pmessage", (pattern, channel, message) => {
    const data = JSON.parse(message);
    const recipientId = channel.split(".")[1]; // Extract recipient ID
    io.to(recipientId).emit("private-message", data);
});

// Handle Socket.IO connections
io.on("connection", (socket) => {
    console.log("A user connected");

    // Join user's private room
    socket.on("join", (userId) => {
        socket.join(userId);
        console.log(`User ${userId} joined their private room.`);
    });

    // Handle disconnections
    socket.on("disconnect", () => {
        console.log("A user disconnected");
    });
});

httpServer.listen(3000, () => {
    console.log("Socket.IO server running on port 3000");
});
