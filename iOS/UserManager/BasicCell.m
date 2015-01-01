//
//  BasicCell.m
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/28/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "BasicCell.h"

@implementation BasicCell

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        //[self.contentView setBackgroundColor:[UIColor clearColor]];
        //self.textLabel.font = [UIFont fontWithName:@"Optima" size:20.0];
        //[self.textLabel setTextColor:[UIColor blackColor]];
        //self.textLabel.backgroundColor = [UIColor clearColor];
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
