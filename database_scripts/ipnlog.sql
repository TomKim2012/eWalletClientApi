USE [mobilebanking]
GO

/****** Object:  Table [dbo].[IPN_logs]    Script Date: 03/06/2015 11:52:24 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[IPN_logs](
	[log_id] [int] IDENTITY(1,1) NOT NULL,
	[ipn_id] [nvarchar](50) NOT NULL,
	[status] [varchar](50) NOT NULL,
	[description] [varchar](max) NOT NULL,
	[http_status] [varchar](50) NOT NULL,
	[attempt] [varchar](1) NOT NULL,
 CONSTRAINT [PK_IPN_logs] PRIMARY KEY CLUSTERED 
(
	[log_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

