import express from 'express';
import { createServer } from 'node:https';
import http from 'node:http';
import { Server } from 'socket.io';
import fs from 'fs';

const app = express();
const httpsServer = createServer(app);
const httpServer = http.createServer(app);
// const httpsServer = https.createServer({
//   key: fs.readFileSync('/www/server/panel/vhost/cert/qcws.caturnus.com/privkey.pem'),
//   cert: fs.readFileSync('/www/server/panel/vhost/cert/qcws.caturnus.com/fullchain.pem')
// }, app);

const io = new Server(httpServer, {
    cors: {
        origin: 'https://quickcount.caturnus.com',
        credentials: true
    }
})

app.get('/', (req, res) => {
    res.send('<h1>Forbidden</h1>');
});

// socket
io.on('connection', (socket) => {
    console.log('a user connected');
    socket.on('sending-paslon', (res) => {
        console.log('sending : ' + res)
        io.emit('get-paslon', res)
    })
    socket.on('updating-paslon', (res) => {
        console.log('updating : ' + res)
        io.emit('update-paslon', res)
    })
});

httpServer.listen(3131, () => {
    console.log('HTTP server listening on port 3131');
});

// httpsServer.listen(3030, () => {
//     console.log('HTTPS server listening on port 3030');
// });