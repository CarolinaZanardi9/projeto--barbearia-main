USE [barbearia]
GO

/****** Objeto:  Table [dbo].[agendamento]    Data do Script: 28/10/2026 22:32:45 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[agendamento](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[clientes_id] [int] NOT NULL,
	[servicos_id] [int] NOT NULL,
	[profissionais_id] [int] NOT NULL,
	[status_id] [int] NOT NULL,
	[dia] [date] NOT NULL,
	[horario] [time](7) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[agendamento]  WITH CHECK ADD FOREIGN KEY([clientes_id])
REFERENCES [dbo].[clientes] ([id])
GO

ALTER TABLE [dbo].[agendamento]  WITH CHECK ADD FOREIGN KEY([profissionais_id])
REFERENCES [dbo].[profissionais] ([id])
GO

ALTER TABLE [dbo].[agendamento]  WITH CHECK ADD FOREIGN KEY([servicos_id])
REFERENCES [dbo].[servicos] ([id])
GO

ALTER TABLE [dbo].[agendamento]  WITH CHECK ADD FOREIGN KEY([status_id])
REFERENCES [dbo].[status_agendamento] ([id])
GO

