// import express from 'express'
// import axios from "axios";
// import { Socket } from "socket.io";

import express from 'express';
import { createServer } from 'node:http';
import { Server } from 'socket.io';
const httpServer = createServer();

const app = express();
// const server = createServer(app);
// const io = new Server(server);
const io = new Server(httpServer, {
    cors: {
        origin: 'http://127.0.0.1:8000',
        credentials: true,
    },
})

app.get('/', (req, res) => {
    res.send('<h1>Forbidden</h1>');
});

io.on('connection', (socket) => {
    console.log('a user connected');
    socket.on('sending-paslon', (res) => {
        io.emit('get-paslon', res)
    })
});

httpServer.listen(3000, () => {
    console.log('server running at http://localhost:3000');
});