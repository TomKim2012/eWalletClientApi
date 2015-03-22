USE [mobilebanking]
GO

/****** Object:  Table [dbo].[IPN_details]    Script Date: 03/06/2015 11:52:05 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[IPN_details](
	[ipn_no] [int] IDENTITY(1,1) NOT NULL,
	[till_model_id] [int] NOT NULL,
	[ipn_address] [varchar](50) NOT NULL,
	[username] [varchar](50) NOT NULL,
	[password] [nvarchar](100) NOT NULL,
 CONSTRAINT [PK_IPN_details] PRIMARY KEY CLUSTERED 
(
	[ipn_no] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

