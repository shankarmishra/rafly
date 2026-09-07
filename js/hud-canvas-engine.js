/**
 * HUD Canvas Engine — 60 FPS Interactive Telemetry Visualizer
 */
document.addEventListener('DOMContentLoaded', () => {
    const canvasElements = document.querySelectorAll('.hud-canvas-element');
    canvasElements.forEach((canvas) => {
        const type = canvas.dataset.type || 'default';
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        let frame = 0;
        function animate() {
            frame++;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Background grid
            ctx.strokeStyle = 'rgba(0, 240, 255, 0.05)';
            ctx.lineWidth = 1;
            for (let x = 0; x < canvas.width; x += 40) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            for (let y = 0; y < canvas.height; y += 40) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }

            if (type === 'microservices' || type === 'web-development') {
                // Microservices Flow Animation
                const nodes = [
                    { label: 'FRONTEND', x: 100, y: 150 },
                    { label: 'API GATEWAY', x: 300, y: 150 },
                    { label: 'DATABASE', x: 500, y: 80 },
                    { label: 'EDGE CDN', x: 500, y: 220 },
                    { label: 'MICROSERVICE A', x: 700, y: 150 }
                ];
                
                // Connections
                ctx.strokeStyle = 'rgba(0, 240, 255, 0.3)';
                ctx.setLineDash([4, 4]);
                ctx.beginPath();
                ctx.moveTo(nodes[0].x, nodes[0].y); ctx.lineTo(nodes[1].x, nodes[1].y);
                ctx.moveTo(nodes[1].x, nodes[1].y); ctx.lineTo(nodes[2].x, nodes[2].y);
                ctx.moveTo(nodes[1].x, nodes[1].y); ctx.lineTo(nodes[3].x, nodes[3].y);
                ctx.moveTo(nodes[2].x, nodes[2].y); ctx.lineTo(nodes[4].x, nodes[4].y);
                ctx.moveTo(nodes[3].x, nodes[3].y); ctx.lineTo(nodes[4].x, nodes[4].y);
                ctx.stroke();
                ctx.setLineDash([]);

                // Floating data packets
                const packetX = (frame * 3) % canvas.width;
                ctx.fillStyle = '#00f0ff';
                ctx.shadowColor = '#00f0ff';
                ctx.shadowBlur = 10;
                ctx.beginPath();
                ctx.arc(packetX, 150, 5, 0, Math.PI * 2);
                ctx.fill();
                ctx.shadowBlur = 0;

                // Render Nodes
                nodes.forEach(n => {
                    ctx.fillStyle = 'rgba(10, 20, 35, 0.9)';
                    ctx.strokeStyle = '#00f0ff';
                    ctx.lineWidth = 1.5;
                    ctx.beginPath();
                    ctx.roundRect(n.x - 65, n.y - 25, 130, 50, 8);
                    ctx.fill();
                    ctx.stroke();

                    ctx.fillStyle = '#ffffff';
                    ctx.font = '12px Space Grotesk, sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText(n.label, n.x, n.y + 4);
                });

            } else if (type === 'security' || type === 'web-security') {
                // Defense Shield Animation
                const centerX = canvas.width / 2;
                const centerY = canvas.height / 2;
                const radius = 80 + Math.sin(frame * 0.05) * 5;

                // Outer Shield Ring
                ctx.strokeStyle = '#00f0ff';
                ctx.shadowColor = '#00f0ff';
                ctx.shadowBlur = 15;
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
                ctx.stroke();
                ctx.shadowBlur = 0;

                // Radar Scan Line
                const angle = frame * 0.03;
                ctx.strokeStyle = 'rgba(123, 47, 252, 0.6)';
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.lineTo(centerX + Math.cos(angle) * radius, centerY + Math.sin(angle) * radius);
                ctx.stroke();

                // Shield Badges
                ctx.fillStyle = '#00f0ff';
                ctx.font = '14px Space Grotesk, monospace';
                ctx.textAlign = 'center';
                ctx.fillText('[WAF ACTIVE]  [TLS 1.3 ENCRYPTED]  [ZERO THREATS]', centerX, centerY + 120);

            } else {
                // Generic Telemetry Wave / Funnel
                ctx.strokeStyle = 'rgba(0, 240, 255, 0.5)';
                ctx.lineWidth = 2;
                ctx.beginPath();
                for (let x = 0; x < canvas.width; x += 10) {
                    const y = canvas.height / 2 + Math.sin((x + frame * 4) * 0.02) * 30;
                    if (x === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                }
                ctx.stroke();
            }

            requestAnimationFrame(animate);
        }
        animate();
    });
});
