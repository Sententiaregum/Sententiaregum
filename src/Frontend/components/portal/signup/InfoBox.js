/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React               from 'react';
import Translate           from 'react-translate-component';
import DismissableAlertBox from '../../app/markup/DismissableAlertBox';

/**
 * Infobox which renders the information alert box in the create account page.
 *
 * @returns {React.Element} The markup.
 */
const InfoBox = () => {
  return (
    <DismissableAlertBox bsStyle="info">
      <Translate content="pages.portal.create_account.info_box" />
    </DismissableAlertBox>
  );
};

export default InfoBox;
